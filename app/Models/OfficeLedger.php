<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLedger extends Model
{
    use HasFactory;

    protected $table = 'office_ledgers'; // make sure this matches your table name

    protected $fillable = [
        'entry_date',     // date
        'category',       // string (nullable allowed)
        'direction',      // 'in' | 'out'
        'source_type',    // 'fund' | 'transaction'
        'source_id',      // int
        'amount',         // numeric
        'mode_of_payment',// 'cash' | 'upi' | ...
        'paid_by',        // string
        'received_by',    // string|null
        'description',    // text|null
        'status',         // 'active' | 'deleted'
    ];

    protected $casts = [
        'entry_date' => 'date:Y-m-d',
        'amount'     => 'decimal:2',
    ];

    /** Scope only active rows (if 'status' exists). */
    public function scopeActive($query)
    {
        $hasStatus = (new static)->isFillable('status');
        return $hasStatus ? $query->where('status', 'active') : $query;
    }

    /** Convenience accessor */
    public function getSignedAmountAttribute(): float
    {
        $amt = (float) $this->amount;
        return $this->direction === 'out' ? -1 * $amt : $amt;
    }
}
