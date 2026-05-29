<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(){
        $paymentMethods = \App\Models\PaymentMethod::latest()->paginate(10);
        return view('admin.payment.manage', compact('paymentMethods'));
    }


public function add() {
    return view('admin.payment.add');
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'logo' => 'image|nullable',
        ]);

        $data = $request->except(['api_public_key', 'api_secret_key', 'account_name', 'account_number', 'logo', 'qr_code']);
        // បញ្ចូល Credentials ទៅជា JSON
        if ($request->type == 'direct_integration') {
            $data['credentials'] = [
                'public_key' => $request->api_public_key,
                'secret_key' => $request->api_secret_key,
                'environment' => $request->environment
            ];
        } elseif ($request->type == 'manual_bank') {
            $data['credentials'] = [
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ];
        }

        // គ្រប់គ្រងរូបភាព
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('payments', 'public');
        }
        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('payments', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment.manage')->with('success', 'Payment method added successfully!');
    }
    // ១. បង្ហាញទំព័រ Edit
public function edit($id)
{
    $method = PaymentMethod::findOrFail($id);
    return view('admin.payment.edit', compact('method'));
}

// ២. អនុវត្តការ Update
public function update(Request $request, $id)
{
    $method = PaymentMethod::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required',
        'type' => 'required',
        'logo' => 'image|nullable',
    ]);

    $data = $request->except(['api_public_key', 'api_secret_key', 'account_name', 'account_number', 'logo', 'qr_code']);

    // បញ្ចូល Credentials ថ្មី
    if ($request->type == 'direct_integration') {
        $data['credentials'] = [
            'public_key' => $request->api_public_key,
            'secret_key' => $request->api_secret_key,
            'environment' => $request->environment
        ];
    } elseif ($request->type == 'manual_bank') {
        $data['credentials'] = [
            'account_name' => $request->account_name,
            'account_number' => $request->account_number
        ];
    } else {
        $data['credentials'] = null;
    }

    // គ្រប់គ្រងរូបភាពថ្មី (បើមាន)
    if ($request->hasFile('logo')) {
        // លុបរូបភាពចាស់ចេញ (ស្រេចចិត្ត)
        $data['logo'] = $request->file('logo')->store('payments', 'public');
    }
    if ($request->hasFile('qr_code')) {
        $data['qr_code'] = $request->file('qr_code')->store('payments', 'public');
    }

    $method->update($data);

    return redirect()->route('admin.payment.manage')->with('success', 'Payment method updated successfully!');
}

// ៣. លុប
public function destroy($id)
{
    $method = PaymentMethod::findOrFail($id);
    $method->delete();
    return redirect()->route('admin.payment.manage')->with('success', 'Payment method deleted successfully!');
}
}
