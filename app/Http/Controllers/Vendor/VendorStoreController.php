<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorStoreController extends Controller
{
    public function index(){
        return view('vendor.store.create');
    }
    public function manage(){
        $userid = Auth::user()->id;
        $stores = Store::where('user_id', $userid)->get();
        return view('vendor.store.manage', compact('stores'));
    }

    public function store(Request $request )
    {
        $validated = $request->validate([
            'store_name' => 'required|unique:stores|max:100|min:3',
            'slug' => 'nullable|unique:stores|max:100|min:3',
'details' => 'nullable|string',
        ]);

        Store::create([
            'store_name' => $request->store_name,
            'slug' => $request->slug ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->store_name),
            'details' => $request->details,
            'user_id' => Auth::user()->id
        ]);

        return redirect()->back()-> with('success', 'Store Added Successfully');
    }

     public function showstore($id){
        $store_info = Store::find($id);
        return view('vendor.store.edit', compact('store_info'));
    }

    public function updatestore(Request $request, $id){
        $store = Store::findOrFail($id);
        $validate_data = $request->validate([
            'store_name' => 'unique:stores|max:100|min:3',
            'slug' => 'unique:stores|max:100|min:3',
            'details' => 'nullable|string|max:500',
        ]);
        $store -> update($validate_data);

        return redirect()-> back() -> with('success', 'Store Updated Successfully');
    }

    public function deletestore($id){
        Store::find($id)->delete();
        return redirect()->back()->with('success', 'Store Deleted Successfully');
    }
}
