<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    // បង្ហាញបញ្ជីសំណើទាំងអស់សម្រាប់ Admin
    public function index()
    {
        $requests = PayoutRequest::with('vendor.user')
                        ->latest()
                        ->paginate(10);
        return view('admin.payouts.index', compact('requests'));
    }

    // អនុម័តសំណើដកប្រាក់
    public function approve($id)
    {
        $payout = PayoutRequest::findOrFail($id);

        // ពិនិត្យមើលថាវា pending ទើបអាច approve បាន
        if ($payout->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is already processed.');
        }

        $payout->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Payout request has been approved!');
    }
    // បដិសេធសំណើដកប្រាក់
    public function reject($id)
    {
        $payout = PayoutRequest::findOrFail($id);

        if ($payout->status !== 'pending') {
            return redirect()->back()->with('error', 'This request is already processed.');
        }

        $payout->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Payout request has been rejected.');
    }

    public function systemReport()
    {
        // ១. សរុបចំនួន Vendor សរុប
        $totalVendors = \App\Models\User::where('role', 'vendor')->count();

        // ២. សរុបទឹកប្រាក់សំណើដកប្រាក់តាមស្ថានភាព
        $reportData = [
            'pending_payouts'   => \App\Models\PayoutRequest::where('status', 'pending')->sum('amount'),
            'approved_payouts'  => \App\Models\PayoutRequest::where('status', 'approved')->sum('amount'),
            'rejected_payouts'  => \App\Models\PayoutRequest::where('status', 'rejected')->sum('amount'),
            'total_payouts'     => \App\Models\PayoutRequest::sum('amount'),
        ];

        // ៣. ទិន្នន័យលម្អិតសម្រាប់តារាងរបាយការណ៍
        $payouts = \App\Models\PayoutRequest::with('user')->latest()->paginate(20);

        return view('admin.reports.index', compact('reportData', 'payouts', 'totalVendors'));
    }

}
