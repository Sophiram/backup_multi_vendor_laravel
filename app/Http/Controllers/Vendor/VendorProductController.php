<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendorProductController extends Controller
{
    private function getVendorId() {
        return Auth::user()->vendor->id ?? null;
    }
    public function index() {
        $vendorId = $this->getVendorId();
        if (!$vendorId) return redirect()->back()->with('error', 'Vendor profile required.');

        $stores = Store::where('vendor_id', $vendorId)->get();
        $availableAttributes = Attribute::all();
        $categories = Category::all();
        return view('vendor.product.create', compact('stores', 'availableAttributes', 'categories'));
    }

    public function manage(){
        $vendorId = $this->getVendorId();
        $availableAttributes = Attribute::all();
        $categories = Category::all();

        $products = Product::where('vendor_id', $vendorId)
            ->with(['attributes.attribute', 'attributes.attributeValue'])
            ->get();

        return view('vendor.product.manage', compact('products', 'availableAttributes', 'categories'));
    }

    public function storeproduct(Request $request){
        $vendorId = $this->getVendorId();
        $validated = $request->validate([

            'product_name' => 'required|string|max:250',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id', // ប្តូរពី nullable ទៅ required
            'store_id' => ['required', \Illuminate\Validation\Rule::exists('stores', 'id')->where('vendor_id', $vendorId)],
            'regular_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'nullable|exists:attributes,id',
            'attributes.*.attribute_value_id' => 'nullable|exists:attribute_values,id',
            'attributes.*.additional_price' => 'nullable|numeric|min:0',
        ]);


        $slug = Str::slug($request->product_name);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $product = Product::create([
            'product_name' => $request->product_name,
            'description' => $request->description ?? 'No description provided', // បន្ថែមតម្លៃ Default នេះ
            'sku' => $request->sku,
            'vendor_id' => $vendorId,
            'category_id' => $request->category_id,
            'subcategory_id'   => $request->subcategory_id,
            'store_id' => $request->store_id,
            'regular_price' => $request->regular_price,
            'discounted_price' => $request->discounted_price,
            'tax_rate' => $request->tax_rate,
            'stock_quantity' => $request->stock_quantity,
            'slug' => $request->slug ?? $slug,
            'meta_title' => $request->meta_title ?? null,
            'meta_description' => $request->meta_description ?? null,
        ]);

        // Handle multiple image upload
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $file) {
                $path = $file->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }
        }


            // បន្ថែមនៅខាងលើ foreach នៃ attributes
            $attributes = $request->input('attributes', []);

           if (!empty($attributes)) {
                ProductAttribute::where('product_id', $product->id)->delete();

                foreach ($attributes as $attrData) {
                    // បន្ថែមលក្ខខណ្ឌត្រួតពិនិត្យឱ្យបានច្បាស់លាស់
                    if (isset($attrData['attribute_id']) && !empty($attrData['attribute_id'])) {
                        ProductAttribute::create([
                            'product_id'         => $product->id,
                            'attribute_id'       => $attrData['attribute_id'],
                            'attribute_value_id' => $attrData['attribute_value_id'] ?? null,
                            'additional_price'   => $attrData['additional_price'] ?? 0,
                        ]);
                    }
                }
            }

         return redirect()->back()->with('success', 'Product with Attributes Added Successfully!');
    }

    public function showproduct($id){
        $product_info = Product::with('attributes.attributeValue')->findOrFail($id);
        $authuserid = Auth::id();
        $stores = Store::where('user_id', $authuserid)->get();
        $availableAttributes = Attribute::all();

        return view('vendor.product.edit', compact('product_info', 'stores', 'availableAttributes'));
    }

    public function updateproduct(Request $request, $id)
    {
        $vendorId = $this->getVendorId();

        $product = Product::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();

        if ($product->vendor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 3. Validation
        $validate_data = $request->validate([
            'product_name' => 'required|string|max:250',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:sub_categories,id',
            'store_id' => ['required', \Illuminate\Validation\Rule::exists('stores', 'id')->where('vendor_id', $vendorId)],
             'discounted_price' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.attribute_value_id' => 'nullable|exists:attribute_values,id',
            'attributes.*.additional_price' => 'nullable|numeric|min:0',
        ]);

        // 4. Update using Transaction to ensure data consistency
        DB::transaction(function () use ($request, $product) {
            // Update Product Details
            $product->update([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'sku' => $request->sku,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id ?: null,
                'store_id' => $request->store_id,
                'regular_price' => $request->regular_price,
                'discounted_price' => $request->discounted_price,
                'tax_rate' => $request->tax_rate,
                'stock_quantity' => $request->stock_quantity,
                'slug' => $request->slug ?: Str::slug($request->product_name),
                // 'meta_title' => $request->meta_title,
                // 'meta_description' => $request->meta_description,
                'meta_title' => $request->meta_title ?? $product->meta_title,
                'meta_description' => $request->meta_description ?? $product->meta_description,
            ]);

            // Handle Image Uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('product_images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => false,
                    ]);
                }
            }

            // Refresh Attributes: Delete old associations and insert new ones
            ProductAttribute::where('product_id', $product->id)->delete();
            $attributes = $request->input('attributes', []);

            foreach ($attributes as $attrData) {
                if (is_array($attrData) && !empty($attrData['attribute_id'])) {
                    ProductAttribute::create([
                        'product_id'         => $product->id,
                        'attribute_id'       => $attrData['attribute_id'],
                        'attribute_value_id' => $attrData['attribute_value_id'] ?? null,
                        'additional_price'   => $attrData['additional_price'] ?? 0.00,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Product and Attributes Updated Successfully');
    }

    public function deleteproduct($id){
        $vendorId = $this->getVendorId();
        $product = Product::where('id', $id)->where('vendor_id', $vendorId)->firstOrFail();


        // Delete product attributes first
        ProductAttribute::where('product_id', $product->id)->delete();

        // Delete product images if needed
        ProductImage::where('product_id', $product->id)->delete();

        // Delete the product
        $product->delete();

        return redirect()->back()->with('success', 'Product Deleted Successfully');
    }
}
