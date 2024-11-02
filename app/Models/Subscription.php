<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions'; // Ensure this matches your actual table name

    // Add all fields you want to allow for mass assignment
    protected $fillable = [
        'user_id',
        'product_id',
        'start_date',
        'end_date',
        'is_active',
    ];
}
