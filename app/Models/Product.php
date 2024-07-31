<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    // Mass assignable attributes
    protected $fillable = [
        'product_id',
        'item_name',
        'slug',
        'product_type',
        'status',
    ];

    // Disable timestamps if not needed
    public $timestamps = true;
}
