<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeController extends Controller
{
    // បង្ហាញទំព័របង្កើត Default Attribute
    public function index() {
        return view('admin.attribute.create');
    }

    // រក្សាទុក Default Attribute និង Values របស់វា
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'values' => 'required|array',
            'values.*' => 'required|string|max:255',
        ]);

        // ១. បង្កើតឈ្មោះ Attribute (ឧទាហរណ៍: Size)
        $attr = Attribute::create(['name' => $request->name]);

        // ២. បង្កើត Values ទាំងអស់ (ឧទាហរណ៍: S, M, L)
        foreach ($request->values as $value) {
            AttributeValue::create([
                'attribute_id' => $attr->id,
                'value' => $value
            ]);
        }

        return redirect()->back()->with('success', 'Default Attribute Created Successfully');
    }


}
