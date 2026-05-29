<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'type',
        'logo',
        'qr_code',
        'credentials', // ត្រូវតែមានព្រោះលោកអ្នកបានកែច្នៃវាជា array នៅក្នុង Controller
    ];
    protected $casts = [
        'credentials' => 'array',
    ];
}
