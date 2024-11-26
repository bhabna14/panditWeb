<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'data', 'is_read'];

    protected $casts = [
        'data' => 'array', // Make sure data is stored as an array
    ];
}