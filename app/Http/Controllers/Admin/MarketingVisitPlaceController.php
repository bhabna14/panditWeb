<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingVisitPlace;

class MarketingVisitPlaceController extends Controller
{
    public function getVisitPlace()
    {
        return view('admin.flower-request.flower-marketing-visit-place');
    }
}
