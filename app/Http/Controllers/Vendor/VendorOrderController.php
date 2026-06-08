<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipping;
use App\Models\Store;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorOrderController extends Controller
{
    /**
     * Display Vendor Sales History with Pre-generated Checkout Shipping Information
     */
    public function vendorIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user->vendor) {
            abort(403, 'You do not have a vendor account.');
        }

        $vendorId = $user->vendor->id;

        $vendor_store_ids = Store::where('vendor_id', $vendorId)->pluck('id');

        $orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->with(['user', 'shipping', 'items' => function($q) use ($vendor_store_ids) {
                $q->whereHas('product', function($pQuery) use ($vendor_store_ids) {
                    $pQuery->whereIn('store_id', $vendor_store_ids);
                });
            }])
            ->orderByDesc('created_at')
            ->paginate(10);

        // 💡 កាត់ចោល $shippingCompanies Query ចេញពីទីនេះ ព្រោះលែងត្រូវការឱ្យ Vendor រើសជាន់គ្នានៅលើ Modal ទៀតហើយ
        return view('vendor.orders.orderhistory', compact('orders'));
    }

    /**
     * Display Detailed Order View and Track Logs
     */
    public function vendorShowOrder($id)
    {
        $order = Order::with(['items.product.images', 'items.product.store', 'user', 'shipping'])
                    ->findOrFail($id);

        $user = Auth::user();
        if (!$user->vendor) {
            abort(403, 'You do not have a vendor account.');
        }

        $vendorId = $user->vendor->id;

        $order->items = $order->items->filter(function ($item) use ($vendorId) {
            return ($item->product->store->vendor_id ?? null) == $vendorId;
        });

        if ($order->items->isEmpty()) {
            abort(403, 'You do not have permission to view this order.');
        }

        // 💡 កាត់ចោល $shippingCompanies Query ចេញពីទីនេះដូចគ្នា ដើម្បីឱ្យកូដដំណើរការលឿន (Lightweight)
        return view('vendor.orders.ordershow', compact('order'));
    }

   public function updateStatus(Request $request, Order $order)
    {
        // Eager load payment ដើម្បីអាចពិនិត្យស្ថានភាពបាន
        $order->load('payment');

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        // 🔒 Business Logic: កុំឱ្យ Completed បើសិនជាមិនទាន់ Paid
        if ($request->status == 'completed') {
            if (!$order->payment || $order->payment->status !== 'paid') {
                return redirect()->back()->with('error', 'Cannot complete this order because the payment has not been confirmed yet.');
            }
        }

        DB::transaction(function () use ($request, $order) {
            $order->update(['status' => $request->status]);

            // 🚚 ប្រសិនបើប្តូរទៅជា Shipped
            if ($request->status == 'shipped') {
                $shipping = Shipping::where('order_id', $order->id)->first();
                if ($shipping) {
                    $shipping->update([
                        'shipping_status' => 'Shipped',
                        'shipped_at'      => now(),
                        'notes'           => $request->notes ?? $shipping->notes,
                    ]);
                }
            }

            // 🟢 ប្រសិនបើប្តូរទៅជា Completed
            if ($request->status == 'completed') {
                Shipping::where('order_id', $order->id)->update([
                    'delivered_at'    => now(),
                    'shipping_status' => 'delivered'
                ]);
            }
        });

        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        return redirect()->back()->with('success', 'Order status updated and customer notified successfully!');
    }
}
