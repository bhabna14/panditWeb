<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FCMNotification extends Model
{
    use HasFactory;

    protected $table = 'f_c_m_notifications';

    protected $fillable = [
        'title',
        'description',
        'image',
        'audience',     // 'all' | 'users' | 'platform'
        'user_ids',     // ["ALL"] OR ["USER30382", ...]
        'platforms',    // ["android","ios","web"]
        'status',
        'success_count',
        'failure_count',
    ];

    protected $casts = [
        'user_ids'  => 'array',
        'platforms' => 'array',
    ];

    // Expose computed field in JSON
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If already absolute (e.g. CDN URL), return as-is
        if (preg_match('#^https?://#i', $this->image)) {
            return $this->image;
        }

        // Otherwise, build a public URL from storage ("public" disk)
        // File should be stored via ->store('notifications', 'public')
        return Storage::disk('public')->url($this->image);
    }
}
