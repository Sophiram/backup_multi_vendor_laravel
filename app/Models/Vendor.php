<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commission_rate',
        'approval_status',
        'bank_account_info',
    ];

    // ទំនាក់ទំនង: Vendor ម្នាក់អាចមានហាងច្រើន
    public function stores()
    {
        return $this->hasMany(Store::class, 'vendor_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'vendor_id');
    }

    // បន្ថែម Relationship ទៅកាន់ Payouts
    public function payouts()
    {
        return $this->hasMany(PayoutRequest::class, 'vendor_id');
    }
    public function getTotalEarningsAttribute()
    {
       return $this->orderItems()
        ->whereHas('order', function($q) {
            $q->whereIn('status', ['completed', 'shipped','processing']); // បន្ថែម 'shipped' ចូល
        })->sum('vendor_net_amount');
    }

    // ២. សរុបទឹកប្រាក់ដែលកំពុង Pending
    public function getPendingPayoutsAttribute()
    {
        return $this->payouts()->where('status', 'Pending')->sum('amount');
    }

    // ៣. ទឹកប្រាក់ដែលអាចដកបាន
    public function getAvailableBalanceAttribute()
    {
        $paid = $this->payouts()->whereIn('status', ['Approved', 'Completed'])->sum('amount');
        return $this->total_earnings - $this->pending_payouts - $paid;
    }

    // --- Logic សម្រាប់គណនា Commission ---
    public function getCommissionRateForCategory($categoryId)
    {
        $rule = CommissionRule::where('category_id', $categoryId)
            ->where('status', 'Active')
            ->first();

        return $rule ? $rule->commission_rate : 0.00;
    }
}
