<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PujaController extends Controller
{
    //
    public function managePuja(){
        return view('admin/managepuja');
    }
    public function addpuja(){
        return view('admin/addpuja');
    }
}
