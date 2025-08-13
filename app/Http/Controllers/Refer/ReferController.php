<?php

namespace App\Http\Controllers\Refer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferController extends Controller
{
    public function offerCreate()
    {
        return view('refer.offer-create');
    }
}
