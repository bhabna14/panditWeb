<?php
// app/Models/OfficeFund.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        // 'status', // include only if your table actually has this column
    ];

    /**
     * If your DB column is DATE, 'date' cast is ideal.
     * If it's DATETIME and you need time precision, switch to 'datetime'.
     */
    protected $casts = [
        'date'   => 'date',        // <— change to 'datetime' if your column is datetime
        'amount' => 'decimal:2',
    ];

    /** Use like: OfficeFund::query()->active() */
    public function scopeActive($query)
    {
        $model = $query->getModel();

        return $model->isFillable('status')
            ? $query->where('status', 'active')
            : $query;
    }
}
