<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poojalist;


class PujaController extends Controller
{
    //
    public function poojalists(){
        $poojalists = Poojalist::where('status', 'active')->get();
        foreach ($poojalists as $poojalist) {
            $poojalist->pooja_img_url = asset('assets/img/' . $poojalist->pooja_photo);
            // dd($poojalist->image_url);
           
        }
        if ($poojalists->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }
        return response()->json($poojalists);
    }
}
