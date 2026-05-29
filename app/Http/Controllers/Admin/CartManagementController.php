<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartManagementController extends Controller
{
    public function index(Request $request)
    {
        // ទាញយកទិន្នន័យ Cart និង Filter
        $query = Cart::with('user')->withCount('items');

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $carts = $query->latest()->paginate(15);

        // គណនាកម្រិតស្ថិតិ
        $totalAbandoned = Cart::where('status', 'abandoned')->count();
        $totalConverted = Cart::where('status', 'converted')->count();

        return view('admin.cart.history', compact('carts', 'totalAbandoned', 'totalConverted'));
    }

    public function show($id)
    {
        $cart = Cart::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.cart.show', compact('cart'));
    }

    public function export(Request $request)
{
    // ទាញយក Query ដូចគ្នានឹង Index
    $query = Cart::with('user');

    // ប្រើ Filter ដូចក្នុង Index
    if ($request->has('search') && $request->search != '') {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    $carts = $query->latest()->get();

    // បង្កើតការ Export (ឧទាហរណ៍ប្រើការបង្កើត CSV សាមញ្ញ)
    $filename = "carts_export_" . date('Y-m-d_His') . ".csv";
    $handle = fopen($filename, 'w+');
    fputcsv($handle, ['Cart ID', 'Customer', 'Status', 'Total Amount', 'Updated At']);

    foreach ($carts as $cart) {
        fputcsv($handle, [
            '#CRT-' . $cart->id,
            $cart->user->name ?? 'Guest',
            ucfirst($cart->status),
            $cart->total_amount,
            $cart->updated_at
        ]);
    }

    fclose($handle);

    return response()->download($filename)->deleteFileAfterSend(true);
}

    public function destroy($id)
        {
            $cart = Cart::findOrFail($id);
            $cart->delete();

            return redirect()->back()->with('success', 'Cart deleted successfully!');
        }

        // សម្រាប់បង្ហាញទំព័រ Edit
public function edit($id)
{
    $cart = Cart::with(['user', 'items.product'])->findOrFail($id);
    return view('admin.cart.edit', compact('cart'));
}

// សម្រាប់រក្សាទុកការកែសម្រួល
public function update(Request $request, $id)
{
    $request->validate(['status' => 'required']);

    $cart = \App\Models\Cart::findOrFail($id);
    $cart->update(['status' => $request->status]);

    return redirect()->route('admin.cart.history')->with('success', 'Cart status updated successfully!');
}
}
