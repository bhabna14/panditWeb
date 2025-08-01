<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerCalendor;
use App\Models\FlowerProduct;


class FlowerCalendarController extends Controller
{
   public function getFestivalCalendar()
   {

    $flowerNames = FlowerProduct::where('category', 'Flower')->where('status','active')->get();

    return view('admin.flower-festival-calendar',compact('flowerNames'));

   }


}
