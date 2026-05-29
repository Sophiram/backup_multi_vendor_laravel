<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class VendorAttributeController extends Controller
{
    // ផ្ទាំងបង្ហាញ Form បង្កើត Attribute
    public function index()
    {
        return view('vendor.attribute.create'); // ត្រូវនឹងទំព័រ Blade ដែលបងមាន
    }

    // មុខងាររក្សាទុកទិន្នន័យ Attribute ចូល Database
    public function store(Request $request)
    {
        $request->validate([
            'attribute_name' => 'required|string|max:255',
            'values'         => 'required|array|min:1',
            'values.*'       => 'required|string|max:255',
        ]);

        // Attribute::create([
        //     'name' => $request->attribute_name,
        // ]);
        $attribute = Attribute::create([
        'name' => $request->attribute_name,
        // 'vendor_id' => auth()->id() // បើប្រព័ន្ធបងមានបែងចែកតាម Vendor
    ]);

        foreach ($request->values as $value) {
        if (!empty($value)) {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value'        => $value,
            ]);
        }
    }

        return redirect()->back()->with('success', 'Attribute Created Successfully!');
    }

    // មុខងារសម្រាប់បង្ហាញតារាងគ្រប់គ្រង (បើបងចង់ធ្វើនៅថ្ងៃក្រោយ)
    public function manage()
    {
        $attributes = Attribute::all();
        return view('vendor.attribute.manage', compact('attributes'));
    }

    public function delete($id)
{
    // 1. ស្វែងរកទិន្នន័យ Attribute តាមរយៈ ID
    $attribute = Attribute::findOrFail($id);

    // 2. លុបគ្រាប់ទិន្នន័យ Value ទាំងអស់ដែលពាក់ព័ន្ធនឹង Attribute នេះជាមុនសិន (ដើម្បីកុំឱ្យទិន្នន័យគាំង/Error ក្នុង DB)
    // សម្គាល់៖ ប្រសិនបើបងបានកំណត់ 'cascade' លើ Foreign Key ក្នុង Migration រួចហើយ វានឹងលុបអូតូ ប៉ុន្តែសរសេរបែបនេះគឺមានសុវត្ថិភាពខ្ពស់
    if ($attribute->values) {
        $attribute->values()->delete();
    }

    // 3. ធ្វើការលុប Attribute ចម្បង
    $attribute->delete();

    // 4. លោតត្រឡប់ទៅវិញជាមួយសារជោគជ័យ
    return redirect()->back()->with('success', 'Attribute and its associated values deleted successfully!');
}

    public function update(Request $request, $id)
    {
    $request->validate([
        'attribute_name' => 'required|string|max:255',
        'values'         => 'required|array|min:1',
        'values.*'       => 'required|string|max:255',
    ]);

    // 1. ស្វែងរក និងធ្វើបច្ចុប្បន្នភាពឈ្មោះ Attribute ចម្បង
    $attribute = Attribute::findOrFail($id);
    $attribute->update([
        'name' => $request->attribute_name
    ]);

    // 2. លុបគ្រាប់តម្លៃ (Values) ចាស់ៗចោលទាំងអស់ជាមុនសិន រួចសរសេរបញ្ចូលថ្មី
    $attribute->values()->delete();

    // 3. លូបបញ្ចូលតម្លៃថ្មីដែល Vendor បានកែក្នុង Pop-up
    foreach ($request->values as $value) {
        if (!empty($value)) {
            AttributeValue::create([
                'attribute_id' => $attribute->id,
                'value'        => $value,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Attribute and values updated successfully inside pop-up!');
    }

}
