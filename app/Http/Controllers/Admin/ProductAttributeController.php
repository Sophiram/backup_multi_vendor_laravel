<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultAttribute;
use Illuminate\Http\Request;
use App\Models\ProductAttribute;

class ProductAttributeController extends Controller
{

    public function index(){
        
        return view('admin.product_attribute.create');
    }

public function manage() {
        // ១. ទាញយក Product Attributes (សម្រាប់តារាងទី ១)
        $attributes = \App\Models\ProductAttribute::with(['product', 'attribute'])->get();

        // ២. ទាញយក Global Attributes (សម្រាប់តារាងទី ២)
        $globalAttributes = \App\Models\Attribute::with('values')->get();

        // ៣. បញ្ជូនទៅ View ទាំងពីរក្នុងពេលតែមួយ
        return view('admin.product_attribute.manage', compact('attributes', 'globalAttributes'));
    }
    // ក្នុងឯកសារ app/Http/Controllers/Admin/ProductAttributeController.php


    public function createattribute(Request $request) {
    $request->validate([
        'product_id' => 'required',
        'attribute_id' => 'required',
        'additional_price' => 'numeric|min:0',
    ]);

    \App\Models\ProductAttribute::create($request->all());

    return redirect()->back()->with('success', 'Attribute Added Successfully');
}

public function updateattribute(Request $request, $id) {
    $attribute_info = \App\Models\ProductAttribute::findOrFail($id);

    $request->validate([
        'product_id' => 'required',
        'attribute_id' => 'required',
        'additional_price' => 'required|numeric|min:0',
    ]);

    // កែសម្រួលទិន្នន័យ
    $attribute_info->update([
        'product_id' => $request->product_id,
        'attribute_id' => $request->attribute_id,
        'additional_price' => $request->additional_price,
    ]);

    return redirect()->route('productattribute.manage')->with('success', 'Attribute Updated Successfully');
}

    public function deleteattribute($id){
        // រកមើល record តាម id ហើយលុបវាចេញ
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();

        return redirect()->back()->with('success', 'Attribute Deleted Successfully');
    }

public function showattribute($id){
    $attribute_info = ProductAttribute::with(['product', 'attribute'])->findOrFail($id);
    $products = \App\Models\Product::all();
    $attributes = \App\Models\Attribute::all();

    return view('admin.product_attribute.edit', compact('attribute_info', 'products', 'attributes'));
}

// សម្រាប់បង្ហាញទំព័រ Edit របស់ Global Attribute
public function editGlobal($id) {
    // ទាញយកព័ត៌មានពី Model Attribute
    $attribute = \App\Models\Attribute::findOrFail($id);

    // បញ្ជូនទៅកាន់ View ឈ្មោះ edit_global
    return view('admin.product_attribute.edit_global', compact('attribute'));
}

// សម្រាប់លុប Global Attribute
public function destroyGlobal($id) {
    $attribute = \App\Models\Attribute::findOrFail($id);
    $attribute->delete();

    return redirect()->back()->with('success', 'Global Attribute Deleted Successfully');
}
}
