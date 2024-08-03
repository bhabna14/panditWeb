<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojaskill extends Model
{
    use HasFactory;
    protected $table = "pandit_poojaskill";
    protected $fillable = ['pandit_id', 'pooja_id', 'pooja_name','pooja_photo'];
    
    public function poojaList()
    {
        return $this->belongsTo(Poojalist::class, 'pooja_id','id');
    }

}
