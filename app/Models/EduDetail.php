<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EduDetail extends Model
{
    use HasFactory;
    protected $table = 'pandit_education';
    protected $fillable = [
        'pandit_id', 'mobile_no',
    ];

}
