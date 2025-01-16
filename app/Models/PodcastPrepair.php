<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PodcastPrepair extends Model
{
    use HasFactory;

    protected $table = 'podcast_prepair';

    protected $fillable = [
        
        'podcast_id',
        'language',
        'podcast_name',
        'deity_category',
        'festival_name',
        'podcast_create_date',
        'estimate_publish_date',
        'podcast_create_status',
        'script_location',
        'story_source',
        'script_verified_by',
        'script_created_by',
        'script_created_date',
        'script_verified_date',
        'script_reject_reason',
        'script_editor',
        'podcast_script_status',
        'podcast_image_path',
        'podcast_video_path',
        'podcast_audio_path',
        'podcast_recording_by',
        'recording_date',
        'recording_complete_url',
        'podcast_recording_status',
        'editing_date',
        'music_source',
        'audio_edited_by',
        'editing_verified_by',
        'editing_verified_date',
        'podcast_editing_status',
        'editing_complete_url',
        'editing_reject_reason',
        'youtube_post_date',
        'facebook_post_date',
        'instagram_post_date',
        'youtube_post_link',
        'facebook_post_link',
        'instagram_post_link',
        'final_podcast_type',
        'final_podcast_url',
        'podcast_status',
    ];


    public function publishPodcast()
{
    return $this->belongsTo(PublishPodcast::class, 'podcast_id', 'podcast_id');
}

   
}
