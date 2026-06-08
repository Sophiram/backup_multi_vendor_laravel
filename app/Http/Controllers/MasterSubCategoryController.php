<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // 💡 បានបន្ថែមដើម្បីប្រើប្រាស់មុខងារលុប File រូបភាព

class MasterSubCategoryController extends Controller
{
    /**
     * រក្សាទុក SubCategory ថ្មី
     */
    public function storesubcategory(Request $request)
    {
        // ១. ការផ្ទៀងផ្ទាត់ទិន្នន័យ (Validation)
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'subcategory_name' => 'required|unique:sub_categories,subcategory_name|max:100|min:3',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // ទំហំអតិបរមា 2MB
            'status'           => 'required|in:active,inactive',
        ]);

        // ២. ចាប់យកទិន្នន័យអត្ថបទ
        $data = [
            'category_id'      => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'status'           => $request->status,
        ];

        // ៣. ដំណើរការ Upload រូបភាព (ប្រសិនបើមាន)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // បញ្ជូនទៅកាន់ folder: public/uploads/subcategories/
            $image->move(public_path('uploads/subcategories'), $imageName);

            // រក្សាទុកទីតាំងផ្លូវ Link ទៅក្នុង Database
            $data['image'] = 'uploads/subcategories/' . $imageName;
        }

        // ៤. បង្កើតទិន្នន័យក្នុង Database
        SubCategory::create($data);

        return redirect()->back()->with('success', 'Sub Category Added Successfully');
    }

    /**
     * បង្ហាញទំព័រគ្រប់គ្រង SubCategory ទាំងអស់
     */
    public function manage()
    {
        $subcategories = SubCategory::with('category')->get();
        $categories = Category::all();
        return view('admin.sub_category.manage', compact('subcategories', 'categories'));
    }

    /**
     * បង្ហាញទំព័រ Edit SubCategory
     */
    public function showsubcat($id)
    {
        $subcategory_info = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.sub_category.edit', compact('subcategory_info', 'categories'));
    }

    /**
     * ធ្វើបច្ចុប្បន្នភាព (Update) ទិន្នន័យ SubCategory
     */
    public function updatesubcat(Request $request, $id)
    {
        $subcategory_info = SubCategory::findOrFail($id);

        // ១. Validation
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'subcategory_name' => 'required|max:100|min:3|unique:sub_categories,subcategory_name,' . $id,
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'           => 'required|in:active,inactive',
        ]);

        $data = [
            'category_id'      => $request->category_id,
            'subcategory_name' => $request->subcategory_name,
            'status'           => $request->status,
        ];

        // ២. ដំណើរការប្តូររូបភាពថ្មី និងលុបរូបភាពចាស់ចោល
        if ($request->hasFile('image')) {
            // លុបរូបភាពចាស់ចេញពី Server ប្រសិនបើមានពិតប្រាកដ
            if ($subcategory_info->image && File::exists(public_path($subcategory_info->image))) {
                File::delete(public_path($subcategory_info->image));
            }

            // Upload រូបភាពថ្មីចូល
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/subcategories'), $imageName);

            $data['image'] = 'uploads/subcategories/' . $imageName;
        }

        // ៣. រក្សាទុកការកែប្រែ
        $subcategory_info->update($data);

        return redirect()->back()->with('success', 'SubCategory Updated Successfully');
    }

    /**
     * លុប SubCategory និងឯកសាររូបភាពដែលពាក់ព័ន្ធ
     */
    public function deletesubcat($id)
    {
        $subcategory = SubCategory::findOrFail($id);

        // លុបរូបភាពចេញពី Server មុននឹងលុបទិន្នន័យចេញពី Database
        if ($subcategory->image && File::exists(public_path($subcategory->image))) {
            File::delete(public_path($subcategory->image));
        }

        // លុបកំណត់ត្រាចេញពី Database
        $subcategory->delete();

        return redirect()->back()->with('success', 'SubCategory Deleted Successfully');
    }
}
