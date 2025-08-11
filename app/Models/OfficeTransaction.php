<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeTransaction extends Model
{
    use HasFactory;

    protected $table = 'office_transaction';

    protected $fillable = [
        'date',
        'paid_by',
        'amount',
        'mode_of_payment',
        'categories',
        'description',
    ];

}
