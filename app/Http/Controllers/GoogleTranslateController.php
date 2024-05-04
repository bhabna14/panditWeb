<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class GoogleTranslateController extends Controller
{
    //
    public function googleTranslate()
    {
        return view('translate');
    }

    public function googleTranslateChange(Request $request)
    {
        App::setLocale($request->lang);

        Session::put('locale',$request->lang);

        return redirect()->back();
    }
}
