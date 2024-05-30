<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addressdetail extends Model
{
    use HasFactory;
    protected $table = 'addressdetails';
    protected $fillable = ['pandit_id','preaddress','prepost','predistrict','prestate','precountry','prepincode','prelandmark',
'peraddress','perpost','perdistri','perstate','percountry','perpincode','perlandmark']; 

}
