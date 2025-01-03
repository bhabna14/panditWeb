<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FlowerDashboardController extends Controller
{
    public function flowerDashboard(){

        return view('admin.flower-dashboard');

    }
}
