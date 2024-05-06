<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    //
    public function managelang(){
        return view('admin/managelang');
    }
    public function addlang(){
        return view('admin/addlang');
    }
}
