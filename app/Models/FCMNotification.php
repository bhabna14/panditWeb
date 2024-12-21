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
        'status',
    ];
}
