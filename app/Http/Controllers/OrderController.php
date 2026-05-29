<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items']) // 🚀 បន្ថែមដើម្បីការលឿន
            ->orderByDesc('created_at')
            ->paginate(10);

     $total_stores = Auth::user()->stores()->count();
    $total_products = Auth::user()->products()->count();
    $total_orders = Auth::user()->orders()->count();

    // 🟢 ទាញយកសកម្មភាពថ្មីៗពី Database (បន្ថែមថ្មី)
    $activities = Auth::user()->activities()->latest()->take(4)->get();

        return view('orders.index', compact('orders', 'total_stores', 'total_products', 'total_orders', 'activities'));
    }

    public function show($id)
    {
        // 🚀 ទាញយកទិន្នន័យព្រមទាំងរូបភាពផលិតផលទាំងអស់មកជាមួយគ្នាតែម្តង (ការពារ N+1 Query)
        $order = Order::with(['items.product.images'])->findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()->role !== 0) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()->role !== 0) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Cannot cancel this order');
        }

        // Restore stock
        foreach ($order->items as $item) {
            // កែសម្រួលចំណុច product() មក product ដើម្បីការពារ Bug
            if ($item->product) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('order.index')->with('success', 'Order cancelled successfully');
            }

            /**
         * 🏪 បង្ហាញប្រវត្តិនៃការលក់របស់ Vendor (Vendor Order History)
         * URL: /vendor/order/history
         */
        public function vendorIndex(Request $request)
        {
            $search = $request->input('search');
            $vendor_store_ids = \App\Models\Store::where('user_id', Auth::id())->pluck('id');

            // ទាញយកការកម្មង់ទិញដែលពាក់ព័ន្ធនឹងផលិតផលរបស់ Vendor ម្នាក់នេះ
        $orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->with(['user', 'items.product.store']) // Eager loading ការពារ N+1 query
            ->when($search, function ($query, $search) {
                return $query->where('order_number', 'LIKE', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10); // 🚀 រក្សាទុក Pagination សម្រាប់ firstItem()

            // ហៅទៅកាន់ឯកសារ blade របស់ vendor (ត្រូវប្រាកដថាឈ្មោះ orderhistory.blade.php)
            return view('vendor.orderhistory', compact('orders'));
        }


        
}
