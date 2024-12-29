<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestItem extends Model
{
    use HasFactory;

    protected $table = 'product__request_items';
    
    // Define the fillable properties
    protected $fillable = [
        'flower_request_id',
        'flower_name',
        'flower_unit',
        'flower_quantity',
    ];

    public function flowerRequest()
    {
        return $this->belongsTo(ProductRequest::class);
    }

}
