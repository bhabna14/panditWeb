<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanditCancel extends Model
{
    use HasFactory;
    protected $table = 'pandit_pooja_cancel';
    protected $fillable = [
        'pandit_id', 'booking_id', 'pandit_cancel_reason'
    ];
}
