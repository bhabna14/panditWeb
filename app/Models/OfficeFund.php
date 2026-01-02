<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class OfficeFund extends Model
{
    use HasFactory;

    protected $table = 'office_fund_received';

    protected $fillable = [
        'date',
        'categories',
        'amount',
        'mode_of_payment',
        'paid_by',
        'received_by',
        'description',
        'status', // ✅ add status (so scopeActive can be trusted + future saves)
    ];

    protected $casts = [
        // If your DB column is DATETIME, keep 'datetime'. If it's DATE, you can use 'date'.
        'date'   => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        // ✅ Do not depend on "fillable" to decide whether status exists.
        return Schema::hasColumn($this->getTable(), 'status')
            ? $query->where('status', 'active')
            : $query;
    }
}
