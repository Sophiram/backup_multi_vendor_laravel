<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\CommissionRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string|min:10',
            'billing_address' => 'nullable|string|min:10',
            'payment_method' => 'required|in:credit_card,debit_card,paypal'
        ]);

        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        try {
            DB::beginTransaction();

            // គណនាតម្លៃសរុបរបស់ទំនិញទាំងអស់ក្នុងកន្ត្រកដើម្បីយកទៅដាក់ក្នុង Order និង Payment
            $totalOrderAmount = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'total_amount' => $totalOrderAmount,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
                'payment_method' => $validated['payment_method']
            ]);

            // ២. បង្កើតទិន្នន័យទូទាត់ប្រាក់ដំបូងនៅក្នុងតារាង payments (Status = pending)
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'amount' => $totalOrderAmount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method']
            ]);

            // ៣. បង្កើតទំនិញលម្អិត និងគណនា Commission របស់ Vendor
            // 💡 ឡូដ Eager Loading store.vendor ឱ្យចំតាមរចនាសម្ព័ន្ធ stores មានតែ vendor_id
            foreach ($cart->items()->with('product.store.vendor')->get() as $cartItem) {

                $product = $cartItem->product;

                // ចាប់យក user_id របស់ Vendor តាមរយៈលំហូរ store->vendor->user_id
                $vendor_id = ($product->store && $product->store->vendor) ? $product->store->vendor->user_id : null;

                $item_total_amount = $cartItem->price * $cartItem->quantity;

                // ស្វែងរក % Commission ផ្អែកលើ Category របស់ផលិតផល
                $rule = CommissionRule::where('category_id', $product->category_id)
                    ->where('status', 'Active')
                    ->first();

                $commissionRate = $rule ? $rule->commission_rate : 0.00;

                // គណនាទឹកប្រាក់ដែលក្រុមហ៊ុនត្រូវកាត់ទុក និងប្រាក់ចំណូលសុទ្ធរបស់ Vendor
                $commissionAmount = ($item_total_amount * $commissionRate) / 100;
                $vendor_net_amount = $item_total_amount - $commissionAmount;

                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $cartItem->product_id,
                    'vendor_id'         => $vendor_id,
                    'quantity'          => $cartItem->quantity,
                    'price'             => $cartItem->price,
                    'commission_rate'   => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'vendor_net_amount' => $vendor_net_amount,
                    'total'             => $item_total_amount,
                ]);

                // Reduce stock
                if ($product) {
                    $product->decrement('stock_quantity', $cartItem->quantity);
                }
            }

            // Clear cart
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('order.show', $order->id)->with('success', 'Order placed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error processing order: ' . $e->getMessage());
        }
    }
}
