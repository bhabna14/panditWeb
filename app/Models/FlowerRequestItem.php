<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerRequestItem extends Model
{
    use HasFactory;

    protected $table = 'flower_request_items';

    protected $fillable = [
        'flower_request_id',
        'type',
        'garland_name',
        'flower_count',
        'garland_quantity',
        'garland_size',
        'flower_name',
        'flower_unit',
        'flower_quantity',
        'size'
    ];

    public function flowerRequest()
    {
        return $this->belongsTo(FlowerRequest::class, 'flower_request_id', 'id');
    }
}
