<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastCategory extends Model
{
    use HasFactory;
    protected $table = 'podcast_categories';

    protected $fillable = [
        'category_name',
        'category_img',
        'description',
    ];
}
