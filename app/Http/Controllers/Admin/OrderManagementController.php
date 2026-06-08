<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['user', 'payment']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'LIKE', "%{$searchTerm}%"));
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
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
        $order = Order::with(['user', 'items.product', 'payment'])->findOrFail($id);
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

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully!');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $order = Order::with('payment')->findOrFail($id);

        // ពិនិត្យមើលថាតើ Payment record មានស្រាប់ឬអត់
        if ($order->payment) {
            // បើសិនជា Paid រួចហើយ កុំឱ្យ Update ទៀត
            if (strtolower($order->payment->status) === 'paid') {
                return redirect()->back()->with('error', 'Cannot update. This order has already been paid!');
            }
            $order->payment->update(['status' => $validated['payment_status']]);
        } else {
            // បើមិនទាន់មាន Record ត្រូវបង្កើតថ្មី
            $order->payment()->create([
                'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                'amount' => $order->total_amount,
                'status' => $validated['payment_status'],
                'payment_method' => 'manual'
            ]);
        }

        // បើសិនជា Status គឺ 'paid' ធ្វើការ Update Status របស់ Order
        if ($validated['payment_status'] === 'paid' && $order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function export(Request $request)
    {
        $fileName = 'orders_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new OrdersExport($request->all()), $fileName);
    }
}
