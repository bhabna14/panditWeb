<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanditController extends Controller
{
    //
   
    public function panditlogin(){
        return view("panditlogin");
        // dd("hi");
    }

    public function panditprofile(){
        return view("/pandit/profile");
        // dd("hi");
    }
    
    public function panditdashboard(){
        return view("/pandit/dashboard");
        // dd("hi");
    }

}
