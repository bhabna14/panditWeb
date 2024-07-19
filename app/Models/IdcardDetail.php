<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdcardDetail extends Model
{
    use HasFactory;
    protected $table = 'pandit_idcard';
    protected $fillable = [
        'id_type', 'upload_id',
    ];
}
