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
use App\Models\Poojalist;
use App\Models\Poojaitemlist;
use App\Models\PanditCancel;
use App\Models\Booking;
use App\Models\Poojastatus;
use App\Events\BookingApproved;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PanditController extends Controller
{ 

    public function index()
{
    $today = Carbon::today()->toDateString();
    // $user = Auth::guard('pandits')->user();

    // $profile = Profile::where('pandit_id', $user->pandit_id)->first();

    $pandit = Auth::guard('pandits')->user();

    // Fetch the pandit's profile details using their pandit_id
    $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

    // Fetch bookings for today with application status approved and join with pooja_list to get the pooja name
    $bookings = DB::table('bookings')
                  ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
                  ->where('bookings.pandit_id', $pandit_details->id)
                  ->where('bookings.payment_status', 'paid')
                  ->where('bookings.application_status', 'approved')
                  ->where('bookings.pooja_status','!=','canceled')
                  ->whereDate('bookings.booking_date', $today)
                  ->orderBy('bookings.booking_date', 'asc') // Order by booking_date ascending
                  ->select('bookings.*', 'pooja_list.pooja_name as pooja_name')
                  ->get();

    // Retrieve the status for each booking
    foreach ($bookings as $booking) {
        $booking->status = Poojastatus::where('booking_id', $booking->booking_id)
                                      ->where('pooja_id', $booking->pooja_id)
                                      ->first();
    }

    $pooja_status = DB::table('pooja_status')
                    ->join('pooja_list', 'pooja_status.pooja_id', '=', 'pooja_list.id')
                    ->join('bookings', 'bookings.booking_id', '=', 'pooja_status.booking_id') // Adjusted to join with bookings table
                    ->where('pooja_status.status', 'active')
                    ->where('bookings.pandit_id', $pandit_details->id) // Filter by pandit_id
                    ->select(
                        'pooja_status.start_time',
                        'pooja_status.end_time',
                        'pooja_list.pooja_name',
                        'pooja_status.pooja_status',
                        'pooja_status.pooja_duration as pooja_duration',
                        'pooja_status.booking_id as booking_id' // Include booking_id in the result
                    )
                    ->orderBy('pooja_status.id', 'desc')
                    ->get();
                    // dd($pooja_status);
    $pooja_request = Booking::with(['user', 'pooja', 'address']) // Load relationships to get user, pooja, and address details
                    ->where('pandit_id', $pandit_details->id) // Use id from profile
                    ->orderBy('created_at', 'desc')
                    ->get();

                        
    return view('pandit.dashboard', compact('bookings', 'today', 'pooja_status','pooja_request'));
}
public function poojarequest()
{
    $pandit = Auth::guard('pandits')->user();

    // Fetch the pandit's profile details using their pandit_id
    $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

    // Debugging: Check the authenticated pandit's profile id
    \Log::info('Authenticated pandit profile id:', ['id' => $pandit_details->id]);

    // Fetch bookings for the authenticated pandit using the profile id
    $bookings = Booking::with(['user', 'pooja', 'address']) // Load relationships to get user, pooja, and address details
                       ->where('pandit_id', $pandit_details->id) // Use id from profile
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

        public function rejectBooking(Request $request, $id)
        {
            $booking = Booking::findOrFail($id);
            $booking->status = 'rejected';
            $booking->application_status = 'rejected';
            $booking->payment_status = 'rejected';
            $booking->pooja_status = 'rejected';
            $booking->save();
        
            // Save to PanditCancel table
            $pandit = Auth::guard('pandits')->user();
        
            PanditCancel::create([
                'pandit_id' => $pandit->pandit_id,
                'booking_id' => $request->booking_id,
                'pandit_cancel_reason' => $request->cancel_reason,
            ]);
        
            return redirect()->back()->with('success', 'Booking rejected successfully!');
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
    public function getDetails($id)
    {
        try {
            $booking = Booking::with(['pooja', 'user', 'address'])->find($id);
    
            if (!$booking) {
                return response()->json(['message' => 'Booking not found.'], 404);
            }
    
            $data = [
                'user' => [
                    'name' => $booking->user->name,
                    'mobile_number' => $booking->user->mobile_number,
                ],

                'pooja' => [
                    'pooja_name' => $booking->pooja->pooja_name,
                    'pooja_fee' => $booking->pooja->pooja_fee,
                ],

                'booking_time' => $booking->booking_date,
                'payment_status' => $booking->payment_status,
                'pooja_status' => $booking->pooja_status,
                
                'address' => [
                    'country' => $booking->address->country,
                    'pincode' => $booking->address->pincode,
                    'landmark' => $booking->address->landmark,
                ],
            ];
    
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the booking details.', 'error' => $e->getMessage()], 500);
        }
    }
    
  
  

}
