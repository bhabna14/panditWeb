<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}