<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerRequestItem extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow the naming convention
    protected $table = 'flower_request_items';

    // Define the fillable properties
    protected $fillable = [
        'flower_request_id',
        'flower_name',
        'flower_unit',
        'flower_quantity',
    ];

    /**
     * Relationship with FlowerRequest
     * Each item belongs to one FlowerRequest
     */
    public function flowerRequest()
    {
        return $this->belongsTo(FlowerRequest::class);
    }
}
