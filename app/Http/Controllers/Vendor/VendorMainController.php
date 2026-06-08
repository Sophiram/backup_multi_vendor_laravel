<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Vendor; // បន្ថែម Model នេះ
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class VendorMainController extends Controller
{
    private function getVendorId() {
        return auth()->user()->vendor->id ?? null;
    }
    public function index()
    {
        $vendorId = $this->getVendorId();
        if (!$vendorId) return redirect()->back()->with('error', 'You are not registered as a vendor.');

        $vendor_store_ids = Store::where('vendor_id', $vendorId)->pluck('id');

         $total_stores = $vendor_store_ids->count();
        $total_products = Product::whereIn('store_id', $vendor_store_ids)->count();
        $total_orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
            $query->whereIn('store_id', $vendor_store_ids);
        })->count();

        $monthly_sales = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->where('status', 'delivered')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $month_names = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
        $chart_sales_months = [];
        $chart_sales_data = [];

        foreach ($monthly_sales as $sale) {
            $chart_sales_months[] = $month_names[$sale->month] ?? 'Unknown';
            $chart_sales_data[] = (float)$sale->total;
        }

        $vendorId = auth()->user()->vendor->id;
        $store_performance = DB::table('stores')
            ->join('products', 'products.store_id', '=', 'stores.id')
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('stores.vendor_id', $vendorId)
            ->where('orders.status', 'completed')
            ->select('stores.store_name', DB::raw('SUM(orders.total_amount) as revenue'))
            ->groupBy('stores.id', 'stores.store_name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $chart_store_names = $store_performance->pluck('store_name')->toArray();
        $chart_store_data = $store_performance->pluck('revenue')->toArray();

        $recent_orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $recent_activities = $recent_orders->map(function($order) {
            return (object)[
                'title' => 'New Order #' . $order->id . ' has been placed',
                'icon' => 'shopping-cart',
                'created_at' => $order->created_at,
                'description' => 'Received an order worth $' . number_format($order->total_amount, 2)
            ];
        });

        return view('vendor.dashboard', compact(
            'total_stores',
            'total_products',
            'total_orders',
            'chart_sales_data',
            'chart_sales_months',
            'chart_store_data',
            'chart_store_names',
            'recent_activities'
        ));
    }

    public function orderhistory()
    {
        $vendorId = $this->getVendorId();
        $vendor_store_ids = Store::where('vendor_id', $vendorId)->pluck('id'); // កែពី user_id មក vendor_id

        $orders = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
            $query->whereIn('store_id', $vendor_store_ids);
        })->with(['user', 'items.product.store'])->latest()->get();

        return view('vendor.orderhistory', compact('orders'));
    }

    public function profile()
    {
        $vendor = auth()->user()->load('vendor');
        return view('vendor.profile', compact('vendor'));
    }

    public function settings()
    {
        $vendor = auth()->user();
        return view('vendor.settings', compact('vendor'));
    }

    /* -------------------------------------------------------------------------- */
    /* កែប្រែមុខងារ SALES REPORT នៅទីនេះ                         */
    /* -------------------------------------------------------------------------- */
    public function salesReport(Request $request)
    {
        $vendorId = $this->getVendorId();
        $vendor_store_ids = Store::where('vendor_id', $vendorId)->pluck('id');

        $selected_year = $request->input('year', date('Y'));
        $selected_month = $request->input('month');

        $earnings_query = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
            $query->whereIn('store_id', $vendor_store_ids);
        })->where('status', 'completed');

        if ($selected_year) {
            $earnings_query->whereYear('created_at', $selected_year);
        }
        if ($selected_month) {
            $earnings_query->whereMonth('created_at', $selected_month);
        }
        $total_earnings = $earnings_query->sum('total_amount');

        $items_query = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('products.store_id', $vendor_store_ids)
            ->where('orders.status', 'completed');

        if ($selected_year) {
            $items_query->whereYear('orders.created_at', $selected_year);
        }
        if ($selected_month) {
            $items_query->whereMonth('orders.created_at', $selected_month);
        }
        $total_items_sold = $items_query->sum('order_items.quantity');

        $orders_query = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
            $query->whereIn('store_id', $vendor_store_ids);
        })->where('status', 'completed');

        if ($selected_year) {
            $orders_query->whereYear('created_at', $selected_year);
        }
        if ($selected_month) {
            $orders_query->whereMonth('created_at', $selected_month);
        }
        $total_orders = $orders_query->count();

        $monthly_query = Order::whereHas('items.product', function ($query) use ($vendor_store_ids) {
                $query->whereIn('store_id', $vendor_store_ids);
            })
            ->where('status', 'completed');

        if ($selected_year) {
            $monthly_query->whereYear('created_at', $selected_year);
        }
        if ($selected_month) {
            $monthly_query->whereMonth('created_at', $selected_month);
        }

        $monthly_sales = $monthly_query->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('vendor.sales_report', compact('total_earnings', 'total_items_sold', 'total_orders', 'monthly_sales'));
    }

    public function exportSalesReport()
    {
        $vendor_store_ids = Store::where('user_id', auth()->id())->pluck('id')->toArray();

        $fileName = 'sales_report_' . date('Y_m_d_H_i') . '.xlsx';

        return Excel::download(new SalesReportExport($vendor_store_ids), $fileName);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:male,female',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bank_account_info' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
            ]);

            if ($user->vendor) {
                $user->vendor->update([
                    'store_name' => $user->vendor,
                    'bank_account_info' => $request->bank_account_info,
                ]);
            }

            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $user->image = $request->file('image')->store('profiles', 'public');
            }

            $user->save();
        });

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function destroy($id)
    {
        $vendorId = $this->getVendorId();
        $store = Store::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        if ($store->image) {
            Storage::disk('public')->delete($store->image);
        }

        $store->delete();

        return redirect()->back()->with('success', 'Store deleted successfully!');
    }


    public function becomeVendor(Request $request)
    {
        $request->validate([
            'bank_account_info' => 'required|string',
        ]);

        \App\Models\Vendor::create([
            'user_id'         => Auth::id(),
            'commission_rate' => 10,
            'approval_status' => 'pending',
            'bank_account_info' => $request->bank_account_info,
        ]);

        return redirect()->back()->with('success', 'You have applied to be a vendor!');
    }
}
