<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CommissionRule;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use App\Services\KhqrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $total_stores = Auth::user()->stores()->count();
        $total_products = Auth::user()->products()->count();
        $total_orders = Auth::user()->orders()->count();

        $activities = Auth::user()->activities()->latest()->take(4)->get();

        return view('orders.index', compact('orders', 'total_stores', 'total_products', 'total_orders', 'activities'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product.images', 'items.product.store', 'user'])->findOrFail($id);

        // ត្រួតពិនិត្យសិទ្ធិ: ម្ចាស់ Order ទើបអាចមើលបាន
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('orders.show', compact('order'));
    }



    public function cancel($id)
    {
        $order = Order::with('payment')->findOrFail($id); // Eager load payment

        if ($order->user_id !== Auth::id() && Auth::user()->role !== 0) {
            abort(403);
        }

        // ពិនិត្យមើលតាមរយៈ Relationship (ការពារការ Cancel បើសិនជា Paid រួចហើយ)
        if ($order->payment && $order->payment->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot cancel a paid order.');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Cannot cancel this order');
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('order.index')->with('success', 'Order cancelled successfully');
    }


   public function completeCheckout(Request $request)
    {
        $cartItems = $request->input('cart_items', []);

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        DB::transaction(function () use ($cartItems) {

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . date('YmdHis') . rand(100, 999),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalOrderAmount = 0;

            foreach ($cartItems as $item) {
                $product = Product::with('store.user')->findOrFail($item['product_id']);

                $rule = CommissionRule::where('category_id', $product->category_id)
                    ->where('status', 'Active')
                    ->first();

                $commissionRate = $rule ? $rule->commission_rate : 0.00;
                $totalPrice = $item['price'] * $item['quantity'];
                $totalOrderAmount += $totalPrice;

                $commissionAmount = ($totalPrice * $commissionRate) / 100;
                $vendorNetAmount = $totalPrice - $commissionAmount;

                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $product->id,
                    'vendor_id'         => $product->store->user_id,
                    'quantity'          => $item['quantity'],
                    'price'             => $item['price'],
                    'commission_rate'   => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'vendor_net_amount' => $vendorNetAmount,
                    'total'             => $totalPrice,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            // ២. Update តម្លៃសរុបរបស់ Order
            $order->update(['total_amount' => $totalOrderAmount]);

            // ៣. បង្កើត Payment record ជា Single Source of Truth
            $order->payment()->create([
                'amount' => $totalOrderAmount,
                'status' => 'pending', // កំណត់ដំបូងថា pending
                'payment_method' => 'manual', // ឬតាមអ្វីដែលបងកំណត់
            ]);
        });

        return redirect()->route('order.index')->with('success', 'Order placed successfully!');
    }


        public function generateKhqr(Request $request)
        {
            $request->validate([
                'amount'   => 'required|numeric|min:0.01',
                'currency' => 'nullable|in:USD,KHR',
            ]);

            $khqr   = new KhqrService();
            $result = $khqr->generateQr(
                (float) $request->amount,
                $request->currency ?? 'USD',
                'POS-' . date('YmdHis')
            );

            if ($result['success']) {
                return response()->json([
                    'status' => 'success',
                    'qr'     => $result['qr'],
                    'md5'    => $result['md5'],
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'message' => $result['message'],
            ], 500);
        }


    public function checkKhqrPayment(Request $request)
    {
        $request->validate(['md5' => 'required|string']);

        $khqr   = new KhqrService();
        $result = $khqr->checkPayment($request->md5);

        return response()->json($result);
    }


}
