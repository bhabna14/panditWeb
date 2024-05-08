<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    //

    public function managetitle(){
        return view('admin/managetitle');
    }
    public function addtitle(){
        return view('admin/addtitle');
    }
}
