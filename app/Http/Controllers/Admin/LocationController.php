<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    //
    public function managelocation(){
        return view('admin/managelocation');
    }
    public function addlocation(){
        return view('admin/addlocation');
    }
}
