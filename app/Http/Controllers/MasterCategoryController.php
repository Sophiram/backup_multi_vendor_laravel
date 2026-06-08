<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // 💡 ចាំបាច់ត្រូវថែម ដើម្បីប្រើប្រាស់មុខងារលុប File

class MasterCategoryController extends Controller
{
    /**
     * រក្សាទុក Category ថ្មី
     */
    public function storecategory(Request $request)
    {
        // ១. ការផ្ទៀងផ្ទាត់ទិន្នន័យ (Validation)
        $request->validate([
            'category_name' => 'required|unique:categories,category_name|max:100|min:3',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // ទំហំអតិបរមា 2MB
            'status'        => 'required|in:active,inactive',
        ]);

        // ២. ចាប់យកទិន្នន័យអត្ថបទ
        $data = [
            'category_name' => $request->category_name,
            'status'        => $request->status,
        ];

        // ៣. ដំណើរការ Upload រូបភាព (ប្រសិនបើមាន)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // បញ្ជូនទៅកាន់ folder: public/uploads/categories/
            $image->move(public_path('uploads/categories'), $imageName);

            // រក្សាទុកទីតាំងផ្លូវ Link ទៅក្នុង Database
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        // ៤. បង្កើតទិន្នន័យក្នុង Database
        Category::create($data);

        return redirect()->back()->with('success', 'Category Added Successfully');
    }

    /**
     * បង្ហាញទំព័រ Edit Category
     */
    public function showcat($id)
    {
        $category_info = Category::findOrFail($id);
        return view('admin.category.edit', compact('category_info'));
    }

    /**
     * ធ្វើបច្ចុប្បន្នភាព (Update) ទិន្នន័យ Category
     */
    public function updatecat(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        // ១. Validation (បានកែសម្រួល unique rule កុំឱ្យទាស់ឈ្មោះខ្លួនឯង)
        $request->validate([
            'category_name' => 'required|max:100|min:3|unique:categories,category_name,' . $id,
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'        => 'required|in:active,inactive',
        ]);

        $data = [
            'category_name' => $request->category_name,
            'status'        => $request->status,
        ];

        // ២. ដំណើរការប្តូររូបភាពថ្មី និងលុបរូបភាពចាស់ចោល
        if ($request->hasFile('image')) {
            // លុបរូបភាពចាស់ចេញពី Storage ប្រសិនបើមានពិតប្រាកដ
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }

            // Upload រូបភាពថ្មីចូល
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);

            $data['image'] = 'uploads/categories/' . $imageName;
        }

        // ៣. រក្សាទុកការកែប្រែ
        $category->update($data);

        return redirect()->back()->with('success', 'Category Updated Successfully');
    }

    /**
     * លុប Category និងឯកសាររូបភាពដែលពាក់ព័ន្ធ
     */
    public function deletecat($id)
    {
        $category = Category::findOrFail($id);

        // លុបរូបភាពចេញពី Server មុននឹងលុបទិន្នន័យចេញពី Database
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        // លុបកំណត់ត្រាចេញពី Database
        $category->delete();

        return redirect()->back()->with('success', 'Category Deleted Successfully');
    }
}
