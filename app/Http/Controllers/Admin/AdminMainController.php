<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminMainController extends Controller
{

    public function index()
    {
        $salesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');

            // គណនាសរុបតាមខែ និងឆ្នាំបច្ចុប្បន្ន (2026)
            $amount = \App\Models\Order::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->sum('total_amount');

            $salesData[$monthName] = (float)$amount;
        }
        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();

        // ត្រួតពិនិត្យទិន្នន័យត្រង់នេះ
        // dd($recentOrders);
        // dd($salesData);
        $data = [
                'categoryCount' => Category::count(),
                'productCount' => Product::count(),
                'pendingVendorCount' => User::where('role', 1)->where('is_approved', false)->count(),
                'orderCount' => Order::count(),
                'labels' => json_encode(array_keys($salesData)),
                'values' => json_encode(array_values($salesData)),
                'recentOrders' => $recentOrders,
            ];

        return view('admin.admin', $data);
    }

    public function setting()
    {
        $products = Product::all();
        $homepagesetting = HomePageSetting::first() ?? new HomePageSetting();
        return view('admin.settings', compact('products', 'homepagesetting'));
    }
    function updatehomepagesetting(Request $request){
       $request->validate([
            'discounted_product_id' => 'required|exists:products,id',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'discount_heading' => 'required|string|max:255',
            'discount_subheading' => 'required|string|max:255',
            'featured_product_1_id' => 'nullable|exists:products,id',
            'featured_product_2_id' => 'nullable|exists:products,id',
       ]);

       $homepagesetting = HomePageSetting::first() ?? new HomePageSetting();
       $homepagesetting->fill($request->all());
       $homepagesetting->save();

       return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }


    // public function manage_user(Request $request)
    // {
    //     $stats = [
    //         'total' => User::count(),
    //         'active' => User::where('status', 'active')->count(),
    //         'admins' => User::where('role', 'admin')->count(),
    //         'suspended' => User::where('status', 'suspended')->count(),
    //     ];

    //     // ២. ទាញទិន្នន័យជាមួយនឹង Search & Filter
    //     $users = User::query()
    //         ->when($request->search, function($query, $search) {
    //             $query->where('name', 'like', "%{$search}%")
    //                   ->orWhere('email', 'like', "%{$search}%");
    //         })
    //         ->when($request->role, fn($q, $role) => $q->where('role', $role))
    //         ->when($request->status, fn($q, $status) => $q->where('status', $status))
    //         ->latest()
    //         ->paginate(10);
    //     return view('admin.manage.user', compact('users', 'stats'));
    // }

    // public function manage_stores()
    // {
    //     return view('admin.manage.store');
    // }

    // public function cart_history()
    // {
    //     return view('admin.cart.history');
    // }

    public function order_history()
    {
        return view('admin.order.history');
    }

    public function pendingVendors()
    {
        $pendingVendors = User::where('role', 1) // ប្តូរទៅជាលេខ 1 ប្រសិនបើអ្នកប្រើ integer
                              ->where('is_approved', false)
                              ->get();

        return view('admin.pending-vendors', compact('pendingVendors'));
    }


    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'Vendor approved successfully!');
    }


    public function markAsRead($id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return redirect()->route('admin.pending');
    }

    public function exportReport()
{
    $filename = "report_" . date('Y-m-d') . ".csv";

    $callback = function() {
        $handle = fopen('php://output', 'w');
        // បង្កើត header
        fputcsv($handle, ['ID', 'Customer', 'Total', 'Status']);

        // ទាញយកទិន្នន័យពី Database
        // ប្រើ chunk ដើម្បីកុំឱ្យស៊ី Memory ច្រើនបើទិន្នន័យមានចំនួនច្រើន
        Order::chunk(100, function($orders) use ($handle) {
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->total_amount,
                    $order->status
                ]);
            }
        });

        fclose($handle);
    };

    return response()->stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ]);
}

    public function cart_history()
{
    // ទាញយកទិន្នន័យដោយប្រើ paginate ដើម្បីកុំឱ្យទំព័រយឺត
    $carts = \App\Models\Cart::with('user')->latest()->paginate(10);

    // គណនាស្ថិតិសម្រាប់ Dashboard
    $totalAbandoned = \App\Models\Cart::where('status', 'abandoned')->count();
    $totalConverted = \App\Models\Cart::where('status', 'converted')->count();

    return view('admin.cart.history', compact('carts', 'totalAbandoned', 'totalConverted'));
}
}
