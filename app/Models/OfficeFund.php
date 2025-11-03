<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeFund extends Model
{
    use HasFactory;

    protected $table = 'office_fund_received';

    protected $fillable = [
        'date',           // DATE or DATETIME column
        'categories',
        'amount',         // DECIMAL(12,2) recommended
        'mode_of_payment',
        'paid_by',
        'received_by',
        'description',
        // 'status',       // Optional; include only if the column exists
    ];

    // If your DB column is DATE:       use 'date'
    // If it is DATETIME/TIMESTAMP:     use 'datetime:Y-m-d H:i:s'
    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
        'amount' => 'decimal:2',
    ];

    // Optional helper scope to apply "active" if present
    public function scopeActive($q)
    {
        return $this->isFillable('status') ? $q->where('status', 'active') : $q;
    }
}
