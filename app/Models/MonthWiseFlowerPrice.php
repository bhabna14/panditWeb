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

}
