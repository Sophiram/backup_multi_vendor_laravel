<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store; // ត្រូវប្រាកដថាអ្នកមាន Model Store
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function manage_vendor(Request $request)
    {
        $vendors = Vendor::with('user')->get();
        return view('admin.manage.vendor', compact('vendors'));
    }

    public function manage_store(Request $request){
        $stores = Store::all();
        return view('admin.manage.store', compact('stores'));
    }

    public function updateStore(Request $request, $id)
    {
        $store = \App\Models\Store::findOrFail($id);
        $store->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Store status updated successfully!');
    }


    public function updateVendor(Request $request, $id)
    {
        // ១. Validation៖ ត្រូវប្រើឈ្មោះ approval_status ដូចក្នុង form
        $request->validate([
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        // ២. Fetch Vendor (មិនមែន Store ទេ)
        $vendor = \App\Models\Vendor::findOrFail($id);

        // ៣. អាប់ដេតទិន្នន័យ
        $vendor->approval_status = $request->approval_status;
        $vendor->save();

        return redirect()->back()->with('success', 'Vendor status updated successfully.');
    }


}
