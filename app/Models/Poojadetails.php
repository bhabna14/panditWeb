<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poojadetails extends Model
{
    use HasFactory;
    protected $table = "pandit_poojadetails";
    protected $fillable = ['pandit_id', 'pooja_id', 'pooja_name','pooja_photo','pooja_fee','pooja_video','pooja_duration','pooja_done'];

}