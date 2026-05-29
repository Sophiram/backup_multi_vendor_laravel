<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store; // ត្រូវប្រាកដថាអ្នកមាន Model Store
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        // ស្វែងរកហាងទាំងអស់ អាចបន្ថែមការ Filter បាននៅទីនេះ
        $vendors = Store::latest()->paginate(10);
        return view('admin.manage.vendor', compact('vendors'));
    }

    public function edit($id)
    {
        $vendor = Store::findOrFail($id);
        return view('admin.manage.vendor.edit', compact('vendor'));
    }
}
