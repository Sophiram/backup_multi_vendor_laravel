<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
    'title', 'code', 'type', 'value', 'min_requirement',
    'start_date', 'end_date', 'status', 'usage_limit_total', 'limit_per_user'
];


        // សម្រាប់បង្ហាញ Status ជាពណ៌
    public function getStatusBadgeAttribute() {
        return $this->status
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">Active</span>'
            : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">Expired</span>';
    }
}
