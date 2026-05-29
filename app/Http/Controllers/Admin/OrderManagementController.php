<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderManagementController extends Controller
{
    public function index( Request $request)
    {
        // $orders = Order::orderByDesc('created_at')->paginate(20);
        $query = Order::query()->with('user'); // ប្រើ with('user') ដើម្បីកាត់បន្ថយ N+1 query problem

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where('order_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$searchTerm}%"));
            }

            $stats = [
                'completed'  => Order::where('status', 'delivered')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'delivery'   => Order::where('status', 'shipped')->count(),
                'cancelled'  => Order::where('status', 'cancelled')->count(),
            ];

            $orders = $query->latest()->paginate(10);
            return view('admin.order.history', compact('orders', 'stats'));
    }

    public function show($id)
    {
        // $order = Order::findOrFail($id);
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.order.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'shipped') {
            $order->update(['shipped_at' => now()]);
        } elseif ($validated['status'] === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    // public function history()
    // {
    //     // គណនាស្ថិតិសម្រាប់ Cards
    //     $stats = [
    //         'completed' => Order::where('status', 'delivered')->count(),
    //         'processing' => Order::where('status', 'processing')->count(),
    //         'delivery' => Order::where('status', 'shipped')->count(),
    //         'cancelled' => Order::where('status', 'cancelled')->count(),
    //     ];

    //     // ទាញយកបញ្ជី Order ទាំងអស់ជាមួយនឹង Pagination
    //     $orders = Order::latest()->paginate(10);

    //     return view('admin.order.history', compact('stats', 'orders'));
    // }

    public function destroy($id)
{
    $order = Order::findOrFail($id);
    $order->delete();

    return redirect()->back()->with('success', 'លុប Order បានជោគជ័យ!');
}
public function updatePaymentStatus(Request $request, $id)
{
    \Log::info("Updating order payment: " . $id);
    $validated = $request->validate([
        'payment_status' => 'required|in:pending,paid,failed,refunded'
    ]);

    $order = Order::findOrFail($id);
    $order->update(['payment_status' => $validated['payment_status']]);

    // return redirect()->back()->with('success', 'Payment status updated successfully!');
    if($order->save()) {
        return redirect()->back()->with('success', 'Payment status updated!');
    } else {
        return redirect()->back()->with('error', 'Update failed!');
    }
}


    public function export(Request $request)
    {
        // ប្រើឈ្មោះ File ដែលអ្នកចង់បាន
        $fileName = 'orders_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // ហៅទៅកាន់ Class OrdersExport ដែលយើងបានបង្កើត
        return Excel::download(new OrdersExport($request->all()), $fileName);
    }
}
