<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeTransaction extends Model
{
    use HasFactory;

    // If your table name is indeed "office_transaction" keep this. Otherwise use the default plural.
    protected $table = 'office_transaction';

    protected $fillable = [
        'date',
        'paid_by',
        'amount',
        'mode_of_payment',
        'categories',
        'description',
        'status',
    ];

    protected $casts = [
        // Make sure Carbon instances are returned, and arithmetic works
        'date'   => 'date:Y-m-d',
        'amount' => 'decimal:2',
    ];
}
