<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class MasterSubCategoryController extends Controller
{
    public function storesubcategory(Request $request){
        $validate = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_name' => 'required|unique:sub_categories,subcategory_name|max:100|min:3',
        ]);
        SubCategory::create($validate);

        return redirect()->back()-> with('success', 'Sub Category Added Successfully');
    }


        public function manage() {
            $subcategories = SubCategory::with('category')->get();
            // បន្ថែមបន្ទាត់នេះ ដើម្បីទាញយក Category ទាំងអស់
            $categories = Category::all();
            return view('admin.sub_category.manage', compact('subcategories', 'categories'));
        }


        public function showsubcat($id){
            $subcategory_info = SubCategory::find($id);
            // បន្ថែមបន្ទាត់នេះ ដើម្បីឱ្យ Modal អាចបង្ហាញបញ្ជី Category បាន
            $categories = Category::all();
            return view('admin.sub_category.edit', compact('subcategory_info', 'categories'));
        }

    public function updatesubcat(Request $request, $id){
        $subcategory_info = SubCategory::findOrFail($id);
        $validate_data = $request->validate([
            'subcategory_name' => 'required|unique:sub_categories,subcategory_name,' . $id . '|max:100|min:3',
            'category_id' => 'required|exists:categories,id',
        ]);
        $subcategory_info -> update($validate_data);

        return redirect()-> back() -> with('success', 'SubCategory Updated Successfully');
    }

    public function deletesubcat($id){
        SubCategory::find($id)->delete();
        return redirect()->back()->with('success', 'SubCategory Deleted Successfully');
    }
}
