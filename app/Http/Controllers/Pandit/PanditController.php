<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanditController extends Controller
{
    //
   
    public function panditlogin(){
        return view("adminlogin");
        // dd("hi");
    }
    

}
