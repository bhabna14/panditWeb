<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PanditController extends Controller
{
    //
   
    public function panditlogin(){
        return view("panditlogin");
        // dd("hi");
    }

    public function panditprofile(){
 
        $locations = [
            "Acharya Vihar",
            "Jayadev Vihar",
            "Khandagiri",
            "Saheed Nagar",
            "Nayapalli",
            "Patia",
            "Rasulgarh",
            "Chandrasekharpur",
            "Old Town",
            "Unit 1",
            "Unit 2",
            "Unit 3",
            "Unit 4",
            "Unit 5",
            "Unit 6",
            "Unit 7",
            "Unit 8",
            "Unit 9",
            "Unit 10",
            "Unit 11",
        ];
        $Temples = [
            "Lingaraj",
            "Tarini",
            "Iskcon",
            "Ram Mandir",
        ];

        $PujaLists = [
            "Ghee",
            "Chandan",
            "Sindur",
            "Flower",
        ];
   
        return view('/pandit/profile', compact('locations', 'Temples','PujaLists'));
    }
    
    public function panditdashboard(){
        return view("/pandit/dashboard");
        // dd("hi");
    }

}
