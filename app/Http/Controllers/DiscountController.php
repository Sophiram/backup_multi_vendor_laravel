<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index() {
        // ទាញទិន្នន័យដោយតម្រៀបពីថ្មីទៅចាស់ និងប្រើ Pagination
        $discounts = \App\Models\Discount::latest()->paginate(10);

        // បញ្ជូនទិន្នន័យទៅកាន់ view
        return view('admin.discount.manage', compact('discounts'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required',
            'code' => 'required|unique:discounts',
            'value' => 'required|numeric',
            'start_date' => 'required',
        ]);

        // កែសម្រួល Checkbox ដែលមិនបានធីក (នឹងទទួលបានតម្លៃ null)
        $data = $request->all();
        $data['status'] = $request->has('status');
        $data['limit_per_user'] = $request->has('limit_per_user');

        \App\Models\Discount::create($data);

        return redirect()->back()->with('success', 'Discount created successfully!');
    }

    public function create() {
        return view('admin.discount.create');
    }

    public function edit($id) {
    $discount = \App\Models\Discount::findOrFail($id);
    return view('admin.discount.edit', compact('discount'));
}

public function update(Request $request, $id) {
    $discount = \App\Models\Discount::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required',
        'code' => 'required|unique:discounts,code,'.$id,
        'value' => 'required|numeric',
    ]);

    $data = $request->all();
    $data['status'] = $request->has('status') ? 1 : 0;

    $discount->update($data);

    return redirect()->route('admin.discount.manage')->with('success', 'Discount updated successfully!');
}
}
