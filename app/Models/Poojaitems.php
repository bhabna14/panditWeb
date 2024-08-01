<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojaitems extends Model
{
    use HasFactory;
    protected $table = "pandit_poojaitem";
    protected $fillable = [
        'pandit_id',
        'pooja_id',
        'pooja_name',
        'pooja_list',
        'list_quantity',
        // 'list_unit', // Uncomment if you want to add unit to fillable
    ];
    // Add relationships if necessary
    public function pooja()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id', 'id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
