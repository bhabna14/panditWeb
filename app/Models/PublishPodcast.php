<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishPodcast extends Model
{
    use HasFactory;

    protected $table = 'publish_podcast';

    protected $fillable = [
        'podcast_id',
        'podcast_image',
        'podcast_music',
        'podcast_video_url',
        'publish_date',
        'description',
    ];
}
