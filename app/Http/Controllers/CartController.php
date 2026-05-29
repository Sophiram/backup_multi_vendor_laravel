<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
public function index()
    {
        $carts = Cart::with('user')->latest()->paginate(10);
        $totalAbandoned = Cart::where('status', 'abandoned')->count();
        $totalConverted = Cart::where('status', 'converted')->count();

        return view('admin.cart.history', compact('carts', 'totalAbandoned', 'totalConverted'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'items_count' => 'required|integer',
            'total_amount' => 'required|numeric',
        ]);

        Cart::create($request->all());

        return redirect()->back()->with('success', 'Cart added successfully!');
    }
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);
        $product = Product::findOrFail($validated['product_id']);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->discounted_price ?? $product->regular_price
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart');
    }

    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->update(['quantity' => $validated['quantity']]);

        return redirect()->back()->with('success', 'Cart updated');
        // Handled by Livewire Component
    }

    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart');
        // Handled by Livewire Component
    }

    public function clear()
    {
        $user = Auth::user();
        if ($user->cart) {
            $user->cart->items()->delete();
        }

        return redirect()->back()->with('success', 'Cart cleared');
    }

    public function getCount()
    {
        $user = Auth::user();
        $count = $user->cart ? $user->cart->items()->sum('quantity') : 0;

        return response()->json(['count' => $count]);
    }
}
