<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorStoreController extends Controller
{
    public function index() {
        return view('vendor.store.create');
    }

    public function manage() {
        // 🔗 ត្រូវទាញយកតាម vendor_id វិញ
        $vendor = Auth::user()->vendor;
        if (!$vendor) return redirect()->back()->with('error', 'Vendor profile not found.');

        $stores = Store::where('vendor_id', $vendor->id)->get();
        return view('vendor.store.manage', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name'  => 'required|unique:stores,store_name|max:100|min:3',
            'slug'        => 'nullable|unique:stores,slug|max:100|min:3',
            'details'     => 'nullable|string',
            'store_email' => 'nullable|email',
            'store_phone' => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $vendor = Auth::user()->vendor;
        if (!$vendor) {
            return redirect()->back()->withErrors(['error' => 'You do not have a Vendor profile!']);
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('stores', 'public');
        }

        Store::create([
            'vendor_id'   => $vendor->id, // 🔗 ប្រើ vendor_id តែមួយគត់
            'store_name'  => $request->store_name,
            'slug'        => $request->slug ? Str::slug($request->slug) : Str::slug($request->store_name),
            'details'     => $request->details,
            'store_email' => $request->store_email,
            'store_phone' => $request->store_phone,
            'address'     => $request->address,
            'logo'        => $logoPath,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Store Created Successfully!');
    }

    public function showstore($id) {
        // 🔗 ការពារមិនឱ្យ Vendor ម្នាក់មើលឃើញ Store របស់ Vendor ផ្សេង
        $vendor = Auth::user()->vendor;
        $store_info = Store::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();
        return view('vendor.store.edit', compact('store_info'));
    }

    public function updatestore(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        $store = Store::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();

        $validate_data = $request->validate([
            'store_name' => 'required|max:100|min:3',
            'slug'       => 'nullable|max:100|min:3',
            'details'    => 'nullable|string|max:500',
            'store_email'=> 'nullable|email',
            'store_phone'=> 'nullable|string',
            'address'    => 'nullable|string',
            'logo'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($store->logo) Storage::disk('public')->delete($store->logo);
            $validate_data['logo'] = $request->file('logo')->store('stores', 'public');
        }

        $store->update($validate_data);

        return redirect()->back()->with('success', 'Store Updated Successfully!');
    }

    public function deletestore($id) {
        $vendor = Auth::user()->vendor;
        $store = Store::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();

        if ($store->logo) Storage::disk('public')->delete($store->logo);
        $store->delete();

        return redirect()->back()->with('success', 'Store Deleted Successfully');
    }
}
