<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthWiseFlowerPrice extends Model
{
    use HasFactory;

    protected $table = 'flower__month_wise_price';
        // app/Models/FlowerPickupDetails.php
    protected $fillable = [
       'vendor_id','product_id','start_date','end_date','quantity','unit_id','price_per_unit'
    ];

     public function vendor()
    {
        return $this->belongsTo(FlowerVendor::class, 'vendor_id', 'vendor_id');
    }

    public function product()
    {
        return $this->belongsTo(FlowerProduct::class, 'product_id', 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(PoojaUnit::class, 'unit_id', 'id');
    }
}
