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
        'item_id',
        'variant_id'
        
    ];
    // Add relationships if necessary
    public function pooja()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id', 'id');
    }

    public function item() {
        return $this->belongsTo(Poojaitemlists::class, 'item_id');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
