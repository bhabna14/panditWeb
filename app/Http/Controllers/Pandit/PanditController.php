<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class PanditController extends Controller
{
   
    public function panditlogin(){
        return view("panditlogin");
    }

    
    public function panditprofile(){
        $countries = Country::all();
        $citys = City::all(); 
        $states = State::all(); 


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
        $languages = [
            'English','Odia','Hindi','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
            'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
            'Sanskrit', 'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        $PujaLists = [
            "Ghee",
            "Chandan",
            "Sindur",
            "Flower",
        ];
        $temples = [
            "Ram Mandir",
            "Lingaraj",
            "Megheshwar",
            "Tarini",
        ];
        $pujanames=[
            "Ganesh Puja",
            "Saraswati Puja",
            "Bishwakarma",
            "Rudrabhisekha",
            "Satyanarayan",
        ];
   
        return view('/pandit/profile', compact('languages','countries','locations','states','citys','PujaLists','temples','pujanames'));
    
    }

    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->get();
        return response()->json($states);
    }  

    public function getCity($stateId){
        $city = City::where('state_id', $stateId)->get();
            return response()->json($city);
    }
    
    public function panditdashboard(){
        return view("/pandit/dashboard");
    }

    public function address(){
        return view('/pandit/address');
    }
    public function storeMultipleLocations(Request $request)
    {
    // Retrieve addresses from the textarea input
    $addressesInput = $request->input('addresses');
    
    // Split addresses by newline or comma, depending on your input format
    $addressesArray = preg_split("/[\r\n,]+/", $addressesInput);
    
    // Loop through each address and store it in the database
    foreach ($addressesArray as $address) {
        // You may want to perform validation or other checks here
        
        // Store the address
        Address::create([
            'address' => $address,
        ]);
    }
    
    return redirect()->back()->with('success', 'Locations saved successfully.');
}

}
