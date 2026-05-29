<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorMainController extends Controller
{
    public function index()
{
    $vendor_store_ids = Store::where('user_id', auth()->id())->pluck('id');

    $total_stores = $vendor_store_ids->count();

    $total_products = Product::whereIn('store_id', $vendor_store_ids)->count();

    $total_orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
        $query->whereIn('store_id', $vendor_store_ids);
    })->count();

    return view('vendor.dashboard', compact('total_stores', 'total_products', 'total_orders'));
}

    public function orderhistory()
{
    $vendor_store_ids = Store::where('user_id', auth()->id())->pluck('id');

    $orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
        $query->whereIn('store_id', $vendor_store_ids);
    })->with(['user', 'items.product.store'])->latest()->get();

    return view('vendor.orderhistory', compact('orders'));
}



public function profile()
{
    // ទាញយកព័ត៌មាន Vendor ដែលកំពុង Login
    $vendor = auth()->user();
    return view('vendor.profile', compact('vendor'));
}

    public function settings()
    {
        $vendor = auth()->user();
        return view('vendor.settings', compact('vendor'));
    }

    public function salesReport()
    {
        // ១. ទាញយក ID ហាងទាំងអស់របស់ Vendor
        $vendor_store_ids = Store::where('user_id', auth()->id())->pluck('id');

        // ២. គណនាប្រាក់ចំណូលសរុប (Total Earnings) ពីការលក់ដែលជោគជ័យ (delivered)
        $total_earnings = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
            $query->whereIn('store_id', $vendor_store_ids);
        })->where('status', 'delivered')->sum('total_amount');

        // ៣. រាប់ចំនួនទំនិញដែលលក់ដាច់សរុប (Total Items Sold)
        $total_items_sold = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id') // កែពី :: មកជា .
        ->join('orders', 'order_items.order_id', '=', 'orders.id')       // កែពី :: មកជា .
        ->whereIn('products.store_id', $vendor_store_ids)
        ->where('orders.status', 'delivered')
        ->sum('order_items.quantity');

        // ៤. ទាញទិន្នន័យលក់សរុបប្រចាំខែ (Monthly Sales) សម្រាប់ឆ្នាំបច្ចុប្បន្ន
        $monthly_sales = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->where('status', 'delivered')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('vendor.sales_report', compact('total_earnings', 'total_items_sold', 'monthly_sales'));
    }
    public function update(Request $request)
    {
        // ១. Validate ទិន្នន័យ
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'gender' => 'nullable|in:male,female',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ២. ទាញយក User ដែលកំពុង Login
        $user = Auth::user();

        // ៣. Update ព័ត៌មាន
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;

        // ៤. រក្សាទុករូបភាព (ប្រសិនបើមាន)
        if ($request->hasFile('image')) {
            // លុបរូបភាពចាស់ចេញ (ប្រសិនបើមាន)
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            // Upload រូបភាពថ្មី
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
