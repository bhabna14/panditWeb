<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerRequest extends Model
{
    use HasFactory;
    protected $table = 'flower_requests';

    // Fillable fields for mass assignment
    protected $fillable = [
        'product_id',
        'user_id',
        'address_id',
        'description',
        'suggestion',
        'status',
    ];
}
