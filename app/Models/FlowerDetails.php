<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerDetails extends Model
{
    use HasFactory;

    protected $table = 'flower__details'; // Ensure this matches your actual table name

    protected $fillable = [
        'flower_id',
        'name',
        'image',
        'quantity',
        'unit',
        'price',
    ];
}
