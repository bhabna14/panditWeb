<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerProduct extends Model
{
    use HasFactory;
    protected $table = 'flower_products';
    protected $fillable = ['name', 'price', 'description', 'category', 'stock', 'duration'];
}
