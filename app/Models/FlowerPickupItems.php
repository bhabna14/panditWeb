<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerPickupItems extends Model
{
    use HasFactory;

    protected $table = 'flower__pickup_items';

    protected $fillable = [
        'pick_up_id',
        'flower_id',

        // ACTUAL
        'unit_id',
        'quantity',
        'price',
        'item_total_price',

        // ESTIMATE
        'est_unit_id',
        'est_quantity',
        'est_price',
    ];

    protected $casts = [
        'quantity'          => 'decimal:2',
        'price'             => 'decimal:2',
        'item_total_price'  => 'decimal:2',
        'est_quantity'      => 'decimal:2',
        'est_price'         => 'decimal:2',
    ];

    /** Flower product */
    public function flower()
    {
        return $this->belongsTo(FlowerProduct::class, 'flower_id', 'product_id');
    }

    /** Actual unit */
    public function unit()
    {
        return $this->belongsTo(PoojaUnit::class, 'unit_id', 'id')
            ->withDefault(['unit_name' => 'N/A']);
    }

    /** Estimate unit */
    public function estUnit()
    {
        return $this->belongsTo(PoojaUnit::class, 'est_unit_id', 'id')
            ->withDefault(['unit_name' => 'N/A']);
    }

    /** Header back-reference */
    public function pickup()
    {
        return $this->belongsTo(FlowerPickupDetails::class, 'pick_up_id', 'pick_up_id');
    }
}
