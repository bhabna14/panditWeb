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
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Messaging;

class PanditController extends Controller
{ 

    public function index()
    {
        $today = Carbon::today()->toDateString();
        $pandit = Auth::guard('pandits')->user();
        $panditId = $pandit->pandit_id;
    
        // Get related data from other tables
        $panditProfile = DB::table('pandit_profile')->where('pandit_id', $panditId)->first();
        $panditCareer = DB::table('pandit_career')->where('pandit_id', $panditId)->first();
        $panditEducation = DB::table('pandit_education')->where('pandit_id', $panditId)->first();
        $panditIdcard = DB::table('pandit_idcard')->where('pandit_id', $panditId)->first();
        $panditVedit = DB::table('pandit_vedic')->where('pandit_id', $panditId)->first();
    
        // List of fields to check
        $fieldsToCheck = [
            'title', 'name', 'slug', 'email', 'whatsappno', 'bloodgroup', 
            'profile_photo','gotra','bruti', 'maritalstatus', 'about_pandit', 'language', 'pandit_status'
        ];
        $careerFields = ['qualification', 'total_experience'];
        $educationFields = ['education_type', 'upload_education'];
        $idcardFields = ['id_type', 'upload_id'];
        $vedicFields = ['vedic_type', 'upload_vedic'];
    
        // Initialize counts
        $totalFields = count($fieldsToCheck) + count($careerFields) + count($educationFields) + count($idcardFields) + count($vedicFields);
        $filledFields = 0;
    
        // Check filled fields in pandit_profile
        foreach ($fieldsToCheck as $field) {
            if (!empty($panditProfile->$field)) {
                $filledFields++;
            }
        }
    
        // Check filled fields in pandit_career
        foreach ($careerFields as $field) {
            if (!empty($panditCareer->$field)) {
                $filledFields++;
            }
        }
    
        // Check filled fields in pandit_education
        foreach ($educationFields as $field) {
            if (!empty($panditEducation->$field)) {
                $filledFields++;
            }
        }
    
        // Check filled fields in pandit_idcard
        foreach ($idcardFields as $field) {
            if (!empty($panditIdcard->$field)) {
                $filledFields++;
            }
        }
    
        // Check filled fields in pandit_vedit
        foreach ($vedicFields as $field) {
            if (!empty($panditVedit->$field)) {
                $filledFields++;
            }
        }
    
        // Calculate percentage
        $completionPercentage = ($filledFields / $totalFields) * 100;
    
        // Fetch the pandit's profile details using their pandit_id
        $pandit_details = Profile::where('pandit_id', $panditId)->first();
    
        // Fetch bookings for today with application status approved and join with pooja_list to get the pooja name
        $bookings = DB::table('bookings')
            ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
            ->where('bookings.pandit_id', $pandit_details->id)
            ->where('bookings.payment_status', 'paid')
            ->where('bookings.application_status', 'approved')
            ->where('bookings.pooja_status', '!=', 'canceled')
            ->whereDate('bookings.booking_date', $today)
            ->orderBy('bookings.booking_date', 'asc')
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
            ->join('bookings', 'bookings.booking_id', '=', 'pooja_status.booking_id')
            ->where('pooja_status.status', 'active')
            ->where('bookings.pandit_id', $pandit_details->id)
            ->select(
                'pooja_status.start_time',
                'pooja_status.end_time',
                'pooja_list.pooja_name',
                'pooja_status.pooja_status',
                'pooja_status.pooja_duration as pooja_duration',
                'pooja_status.booking_id as booking_id'
            )
            ->orderBy('pooja_status.id', 'desc')
            ->get();
    
        $pooja_request = Booking::with(['user', 'pooja', 'address'])
            ->where('pandit_id', $pandit_details->id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        $requestCounts = Booking::select(DB::raw('DATE(booking_date) as date'), DB::raw('COUNT(*) as count'))
            ->where('pandit_id', $pandit_details->id)
            ->where('payment_status', 'paid')
            ->where('application_status', 'approved')
            ->where('pooja_status', 'pending')
            ->whereDate('booking_date', '>=', Carbon::now()->subMonth())
            ->groupBy(DB::raw('DATE(booking_date)'))
            ->get()
            ->map(function ($item) {
                $details = DB::table('bookings')
                    ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
                    ->whereDate('bookings.booking_date', $item->date)
                    ->select('bookings.booking_id as booking_id', 'pooja_list.pooja_name as pooja')
                    ->get();
    
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                    'details' => $details->toArray(),
                ];
            });
    
        return view('pandit.dashboard', compact('bookings', 'today', 'pooja_status', 'pooja_request', 'requestCounts', 'completionPercentage','panditProfile'));
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
    try {
        Log::info('Attempting to approve booking with ID: ' . $id);

        // Find and approve the booking
        $booking = Booking::findOrFail($id);
        Log::info('Booking found: ' . json_encode($booking));

        $booking->application_status = 'approved';
        $booking->save();
        Log::info('Booking status updated to approved for booking ID: ' . $id);

        // Find all user devices using user_id from the booking
        $factory = (new Factory)->withServiceAccount(config('services.firebase.user.credentials'));
        $messaging = $factory->createMessaging();
        Log::info('Firebase Messaging instance created successfully.');

        $userDevices = UserDevice::where('user_id', $booking->user_id)->get();
        Log::info('User devices fetched: ' . json_encode($userDevices));

        if ($userDevices->isNotEmpty()) {
            foreach ($userDevices as $userDevice) {
                $deviceToken = $userDevice->device_id;
                Log::info('Sending notification to device token: ' . $deviceToken);

                // Prepare the notification message
                $message = CloudMessage::withTarget('token', $deviceToken)
                    ->withNotification(Notification::create(
                        'Booking Approved',
                        "Your booking with ID: {$booking->booking_id} has been approved. Please check your account for details."
                    ))
                    ->withData([
                        'booking_id' => $booking->booking_id,
                        'user_id' => $booking->user_id,
                        'message' => 'Your booking has been approved.',
                    ]);
                Log::info('Notification message prepared: ' . json_encode($message));

                try {
                    $messaging->send($message);
                    Log::info('FCM notification sent successfully to device token: ' . $deviceToken);
                } catch (\Exception $e) {
                    Log::error('Error sending FCM notification to device token ' . $deviceToken . ': ' . $e->getMessage());
                }
            }
        } else {
            Log::warning('No device tokens found for user ID: ' . $booking->user_id);
            return redirect()->back()->with('error', 'Unable to send notification: User device token not found.');
        }

        // Broadcast the event (optional, if needed for real-time updates)
        event(new BookingApproved($booking));
        Log::info('BookingApproved event broadcasted for booking ID: ' . $id);

        return redirect()->back()->with('success', 'Booking approved and user notified successfully!');
    } catch (\Exception $e) {
        Log::error('Error in approveBooking method: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to approve booking.');
    }
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
                'review' => $booking->ratings ? $booking->ratings->feedback_message : 'No feedback available', 
                'image_path' => $booking->ratings ? $booking->ratings->image_path : null, // Fetch image path
                'audio_path' => $booking->ratings ? $booking->ratings->audio_file : null, // Fetch audio path
                
                'booking_time' => $booking->booking_date,
                'payment_status' => $booking->payment_status,
                'pooja_status' => $booking->pooja_status,

                'address' => [
                    'country' => $booking->address->country,
                    'state' => $booking->address->state,
                    'city' => $booking->address->city,
                    'area' => $booking->address->area,
                    'address_type' => $booking->address->address_type,
                    'pincode' => $booking->address->pincode,
                ],
            ];
    
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the booking details.', 'error' => $e->getMessage()], 500);
        }
    }

    public function calenderPooja($date)
    {
        try {
            // Convert the input date to 'YYYY-MM-DD' format
            $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            $pandit = Auth::guard('pandits')->user();
            $panditId = $pandit->pandit_id;

            $pandit_details = Profile::where('pandit_id', $panditId)->first();
            
            // Fetch bookings where the date part of 'booking_date' matches the formatted date
            $bookings = Booking::with(['pooja', 'user', 'address'])
                 ->where('pandit_id', $pandit_details->id)
                ->where('payment_status', 'paid')
                ->where('application_status', 'approved')
                ->where('pooja_status', 'pending')
                ->where('status', 'paid')
                ->whereDate('booking_date', $formattedDate)
                ->get();
    
            if ($bookings->isEmpty()) {
                return response()->json(['message' => 'No bookings found for this date.'], 404);
            }
    
            $data = $bookings->map(function ($booking) {
                return [
                    'user' => [
                        'name' => $booking->user->name,
                    ],
                    'pooja' => [
                        'pooja_name' => $booking->pooja->pooja_name,
                    ],
                    'booking_time' => $booking->booking_date,
                    'payment_status' => $booking->payment_status,
                    'pooja_status' => $booking->pooja_status,
                    'address' => [
                        'country' => $booking->address->country,
                        'state' => $booking->address->state,
                        'city' => $booking->address->city,
                        'area' => $booking->address->area,
                        'address_type' => $booking->address->address_type,
                        'pincode' => $booking->address->pincode,
                    ],
                ];
            });
            
    
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the booking details.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
}
