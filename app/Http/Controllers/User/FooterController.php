<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    public function contactUs()
    {
        return view('user.contact-us');
    }

    public function aboutUs()
    {
        return view('user.about-us');
    }

    public function ourStory()
    {
        return view('user.our-story');
    }

    public function crores()
    {
        return view('user.crores');
    }

    public function privacyPolicy()
    {
        return view('user.privacy-policy');
    }

    public function termsAndConditions()
    {
        return view('user.terms-and-conditions');
    }

    public function businessEnrollment()
    {
        return view('user.business-enrollment');
    }

    public function religiousProvider()
    {
        return view('user.religious-provider');
    }

    public function cancelReturn()
    {
        return view('user.cancel-return');
    }
}
