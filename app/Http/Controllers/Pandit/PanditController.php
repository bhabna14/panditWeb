<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use App\Models\Profile;
use App\Models\IdcardDetail;
use App\Models\Career;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use App\Models\Poojaskill;
use App\Models\Poojaitemlist;
use App\Models\Booking;
use App\Events\BookingApproved;
use Illuminate\Support\Facades\Auth;



class PanditController extends Controller
{ 
    public function index()
    {
        $user = Auth::guard('pandits')->user();
        
        $profile = Profile::where('pandit_id', $user->pandit_id)->first();

        return view('pandit/dashboard', compact('user', 'profile'));
    }

  
    public function poojarequest()
    {
        $pandit = Auth::guard('pandits')->user();
    
        // Debugging: Check the authenticated pandit's pandit_id
        \Log::info('Authenticated pandit id:', ['pandit_id' => $pandit->pandit_id]);
    
        // Fetch bookings for the authenticated pandit
        $bookings = Booking::with(['user', 'pooja', 'address']) // Load relationships to get user, pooja, and address details
                           ->where('pandit_id', $pandit->id)
                           ->orderBy('created_at', 'desc')
                           ->get();
    
        // Debugging: Log the bookings fetched
        \Log::info('Bookings fetched:', ['bookings' => $bookings]);
    
        return view('/pandit/poojarequest', compact('bookings'));
    }
    
        public function approveBooking($id)
        {
            $booking = Booking::findOrFail($id);
            $booking->application_status = 'approved';
            $booking->save();
        // Broadcast the event
        event(new BookingApproved($booking));
            return redirect()->back()->with('success', 'Booking approved successfully!');
        }

        public function rejectBooking($id)
        {
            $booking = Booking::findOrFail($id);
            $booking->application_status = 'rejected';
            $booking->save();

            return redirect()->back()->with('success', 'Booking rejected successfully!');
        }

    public function poojahistory(){
        return view("/pandit/poojahistory");
    }

    public function poojaexperties(){

        $Poojanames = Poojalist::where('status', 'active')->get();

        $profile = Profile::where('status', 'active')->first();

        if (!$profile) {
            return redirect()->back()->withErrors(['danger' => 'No active profile found.']);
        }

        $profileId = $profile->profile_id;
        // Fetch previously selected poojas
        $selectedPoojas = Poojaskill::where('pandit_id', $profileId)->pluck('pooja_id')->toArray();

        // Pass the data to the view
        return view('/pandit/poojaexperties', compact('Poojanames', 'selectedPoojas'));
    }

    public function poojadetails(){
        return view("/pandit/poojadetails");
    }
     
    public function panditsprofile(){
        return view("profile");
    }
   
    public function bank(){
        return view("/pandit/panditbank");
    }
    public function panditaddress(){
        return view("/pandit/panditaddress");
    }
  
    public function panditprofile(){
        $countries = Country::all();
        $citys = City::all(); 
        $states = State::all(); 


     
        $languages = [
            'English','Odia','Hindi','Sanskrit','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
            'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
             'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        $PujaLists = Poojaitemlists::all();
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

        $Poojanames = Poojalist::where('status', 'active')->get();

        $Poojaskills = Poojaskill::where('status', 'active')->get();


        return view('/pandit/profile', compact('languages','countries','locations','states','citys','PujaLists','temples','pujanames','Poojanames','Poojaskills'));
    
    }
    public function panditlogin(){
        // dd("hi");
        return view("panditlogin");
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
    public function panditlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/pandit/panditlogin');
    }


  

}
