<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    //
    public function managepodcast(){
        return view('admin/managepodcast');
    }
}
