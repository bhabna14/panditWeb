<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Youtube extends Model
{
    use HasFactory;

    protected $table = 'youtube_url';

    protected $fillable = [
        'title',
        'youtube_url',
        'description',
        'status',
    ];
}
