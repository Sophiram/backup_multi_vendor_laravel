<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorPayout; // ផ្លាស់ប្តូរមកប្រើ VendorPayout ឱ្យស្របគ្នាទាំងអស់
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class VendorPayoutController extends Controller
{
    /**
     * បង្ហាញទំព័រដកប្រាក់ និងគណនាទឹកប្រាក់សរុបរបស់ Vendor
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->vendor) {
            abort(403, 'You do not have a vendor account.');
        }

        $vendor = $user->vendor; // ទាញយក Vendor object
        // dd($vendor->id);
        // $query = $vendor->orderItems()->whereHas('order', function($q) {
        //     $q->whereIn('status', ['completed', 'shipped']);
        // });

        // // មើល SQL Query ដែលវា generate
        // dd($query->toSql(), $query->getBindings(), $query->sum('vendor_net_amount'));

        // ប្រើ Method ដែលបងបានសរសេរក្នុង Model មកប្រើបានភ្លាមៗ!
        $totalEarnings    = $vendor->total_earnings;
        $totalPending     = $vendor->pending_payouts;
        $availableBalance = $vendor->available_balance;

        // ទាញយកប្រវត្តិការដកប្រាក់
        $payouts = $vendor->payouts()->latest()->paginate(10);

        return view('vendor.payout', compact('availableBalance', 'totalEarnings', 'totalPending', 'payouts', 'user'));
    }


        public function requestPayout(Request $request)
        {
            // ១. ទាញយកទិន្នន័យ Vendor និងពិនិត្យគណនី
            $user = Auth::user();
            $vendor = $user->vendor;

            if (!$vendor) {
                return redirect()->back()->with('error', 'Vendor profile not found.');
            }

            // ២. ការផ្ទៀងផ្ទាត់ទិន្នន័យ (Validation) - បានជួសជុល Syntax
            $request->validate([
                'amount' => [
                    'required',
                    'numeric',
                    'min:1',
                    'max:' . $vendor->available_balance // ដាក់ចូលក្នុង Array របៀបនេះដើម្បីកុំឱ្យខុស Syntax
                ],
            ], [
                'amount.required' => 'Please enter the withdrawal amount.',
                'amount.numeric'  => 'The amount must be a valid number.',
                'amount.min'      => 'The amount must be at least $1.00.',
                'amount.max'      => 'Insufficient balance. You can only withdraw up to $' . number_format($vendor->available_balance, 2),
            ]);

            // ៣. ពិនិត្យព័ត៌មានធនាគារ (Bank Info)
            if (empty($vendor->bank_account_info)) {
                return redirect()->back()->with('error', 'Please configure your bank details in profile settings first.');
            }

            // ៤. ពិនិត្យសមតុល្យទឹកប្រាក់ម្តងទៀតដើម្បីធានាសុវត្ថិភាព (Double Check)
            if ($request->amount > $vendor->available_balance) {
                return redirect()->back()->with('error', 'Insufficient available balance.');
            }

            // ៥. បង្កើតកំណត់ត្រាស្នើសុំដកប្រាក់
            VendorPayout::create([
                'vendor_id'             => $vendor->id,
                'amount'                => $request->amount,
                'bank_details_snapshot' => $vendor->bank_account_info,
                'status'                => 'Pending', // រង់ចាំការពិនិត្យពី Admin
            ]);

            return redirect()->back()->with('success', 'Your payout request has been submitted successfully.');
        }
}
