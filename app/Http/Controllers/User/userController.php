<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Bankdetail;
use App\Models\Childrendetail;
use App\Models\Addressdetail;
use App\Models\IdcardDetail;
use App\Models\Poojalist;
use App\Models\UserAddress;
use App\Models\Profile;
use App\Models\Poojadetails;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Rating;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PDF;
use DB;
use Illuminate\Support\Facades\Hash;


class userController extends Controller
{
    //
    public function userindex(){
        $upcomingPoojas = Poojalist::where('status', 'active')
                        ->where('pooja_date', '>=', now())
                        ->orderBy('pooja_date', 'asc')
                        ->take(3)
                        ->get();
        $otherpoojas = Poojalist::where('status', 'active')
                        ->where(function($query) {
                            $query->whereNull('pooja_date');
                         })
                        ->take(8)
                        ->get();
        $pandits = Profile::where('pandit_status', 'accepted')
                        ->take(6)
                        ->get();
        return view("user/index" , compact('upcomingPoojas','otherpoojas','pandits'));
    }

    // public function userlogin(){
    //     return view("login");
    // }
    public function userlogin(Request $request)
    {
        $referer = $request->input('referer');
        session(['login_referer' => $referer]);

        // Return the login view
        return view('login');
    }
    public function demo(){
        return view("panditlogin");
    }
    public function userauthenticate(Request $request)
    {

        $request->validate([
            'phonenumber' => 'required|string',
            'otp' => 'required',
        ]);
    
        $phonenumber = $request->input('phonenumber');
        $otp = $request->input('otp');
    
        // Retrieve superadmin from the database based on phonenumber number
        $user = User::where('phonenumber', $phonenumber)->first();
    
        if ($user && $user->otp === $otp) {
            // Phone number and otp match
            // Perform user login
            // dd($user->status);
            if($user->application_status == "approved"){
            Auth::guard('users')->login($user);
            return redirect()->intended('/user/dashboard');
            }else{
                return redirect()->back()->withInput()->withErrors(['login_error' => 'Your account is not activated']);

            }
        } else {
            // Invalid phone number or otp
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number']);
        }

    
       
    }
    public function searchPooja(Request $request)
    {
        $search = $request->input('search');
        $pandit_pujas = PanditPuja::whereHas('poojalist', function($query) use ($search) {
            $query->where('pooja_name', 'LIKE', "%{$search}%");
        })->get();
    
        return response()->json($pandit_pujas);
    }

    public function dashboard(){
        return view('user.dashboard');
    }
    // public function userlogout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     return redirect('/');
    // }
    public function userlogout(Request $request)
    {
        // Log out the user
        Auth::guard('users')->logout();
    
        // Invalidate the current session
        $request->session()->invalidate();
    
        // Regenerate the CSRF token to prevent reuse
        $request->session()->regenerateToken();
    
        // Clear specific session data if needed
        $request->session()->forget('login_referer'); // Clear referer URL if set
    
        // Redirect to the home page or any other desired page
        return redirect('/');
    }
    

    public function userregister()
    {
        return view('livewire.signup');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeLoginData(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'userid' => 'required|string',
            'phonenumber' => 'required|string',
            'otp' => 'required|integer'
        ]);

        // Concatenate country code with phone number if applicable
        $phonenumber = $request->input('country_code') . $request->input('phonenumber');

        // Check if a user with this phone number already exists
        $user = User::where('phonenumber', $phonenumber)->first();

        if ($user) {
            // User exists, update the OTP
            $user->otp = $request->input('otp');
        } else {
            // User doesn't exist, create a new one
            $user = new User();
            $user->userid = $request->input('userid');
            $user->phonenumber = $phonenumber;
            $user->otp = $request->input('otp');
        }

        // Save the user (either update or create)
        if ($user->save()) {
            // return redirect()->route('user.otp')->with('success', 'OTP generated successfully.');
            return response()->json(['success' => true, 'message' => 'OTP generated successfully.']);
        } else {
            return redirect()->back()->with('error', 'Failed to save OTP.');
        }
    }
    public function showOtpForm()
    {
        return view('/user/userotp');
    }
    
    public function checkuserotp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);
        
    
        $inputOtp = $request->input('otp');
        
        $user = User::where('otp', $inputOtp)->first();
    
        // Check if user exists and the OTP matches
        if ($user) {
            // Log the user in
            Auth::guard('users')->login($user);
            // Clear the OTP after successful validation
            $user->otp = null;
            $user->save();
    
            // return redirect()->route('myprofile')->with('success', 'Login successful.');
            return response()->json(['success' => true, 'message' => 'OTP validated successfully.']);
        } else {
            // OTP is invalid, redirect back with an error message
            return redirect()->route('user.otp')->with('error', 'Invalid OTP.');
        }
    }
    
    
  
   public function poojalist(){
     $allpoojas = Poojalist::where('status', 'active')
                            ->where(function($query) {
                                $query->whereNull('pooja_date');
                            })
                        ->paginate(6);
        return view('user/poojalist', compact('allpoojas'));
    }
    public function ajaxSearchPooja(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $poojas = Poojalist::where('pooja_name', 'LIKE', '%' . $searchTerm . '%')
                            ->where('status','active')->get();

        return response()->json($poojas);
    }
    public function poojadetails($slug)
    {
        $pooja = Poojalist::where('slug', $slug)->firstOrFail();

        // Fetch the related Poojadetails items along with the Profile
        $pandit_pujas = Poojadetails::with('profile')
            ->where('status','active')
            ->where('pooja_id', $pooja->id)
            ->get();

        return view('user.puja-details', compact('pooja', 'pandit_pujas'));
    }
    public function panditDetails($poojaSlug, $panditSlug)
    {
        $pooja = Poojalist::where('slug', $poojaSlug)->firstOrFail();
        $pandit = Profile::where('slug', $panditSlug)->firstOrFail();
    
        // Log the fetched pooja and pandit details
        Log::info("Fetched Pooja details", ['pooja' => $pooja]);
        Log::info("Fetched Pandit details", ['pandit' => $pandit]);
    
        $poojaDetail = Poojadetails::where('pandit_id', $pandit->pandit_id)
            ->where('pooja_id', $pooja->id)
            ->first();
    
        // Log the fetched pooja detail
        Log::info("Fetched PoojaDetail", ['poojaDetail' => $poojaDetail]);
    
        if (!$poojaDetail) {
            return abort(404, 'Pooja details not found.');
        }
    
        return view('user.pandit-details', compact('pooja', 'pandit', 'poojaDetail'));
    }

    public function panditlist(){
        $pandits = Profile::where('pandit_status', 'accepted')
                            ->paginate(9);
            return view('user/panditlist', compact('pandits'));
       
     }

     public function list($pooja_id , $pandit_id)
     {
         // Fetch the pooja details
         $pooja = Poojalist::where('id', $pooja_id)->firstOrFail();
     
         // Fetch the related Poojadetails items along with the Profile, excluding rejected ones
         $pandit_pujas = Poojadetails::with('profile')
             ->where('pooja_id', $pooja->id)
         
        
             ->get();
     
         // Return a view with the list of pandits
         return view('user.puja-details', compact('pooja', 'pandit_pujas'));
     }
     
     

    
    public function ajaxSearch(Request $request)
    {
        $searchTerm = $request->input('searchTerm');
        $pandits = Profile::where('name', 'LIKE', '%' . $searchTerm . '%')
                             ->where('pandit_status','accepted')->get();

        return response()->json($pandits);
    }

     public function singlePanditDetails($slug)
     {
         // Fetch the single pandit based on the provided slug
         $single_pandit = Profile::where('slug', $slug)->firstOrFail();
 
         // Fetch the related pooja details for this pandit
         $pandit_pujas = Poojadetails::where('pandit_id', $single_pandit->pandit_id)
         ->where('status','active')
             ->with('poojalist') // Load the poojalist relationship
             ->get();
 
         return view('user.single-pandit-detail', compact('single_pandit', 'pandit_pujas'));
     }
     public function bookNow($panditSlug, $poojaSlug, $poojaFee)
    {
        if (!Auth::guard('users')->check()) {
            return redirect()->route('userlogin')->with('message', 'You are not logged in yet. Please log in to book.');
        }
        $user = Auth::guard('users')->user();
        $addresses = UserAddress::where('user_id', $user->userid)->where('status', 'active')->get();
        // Fetch pandit and pooja details based on slugs
        $pandit = Profile::where('slug', $panditSlug)->firstOrFail();
        $pooja = Poojadetails::whereHas('poojalist', function ($query) use ($poojaSlug) {
            $query->where('slug', $poojaSlug);
        })->whereHas('profile', function ($query) use ($pandit) {
            $query->where('id', $pandit->id);
        })->firstOrFail();

        // Pass data to the view
        return view('user.booknow', [
            'pandit' => $pandit,
            'pooja' => $pooja,
            'poojaFee' => $poojaFee,
            'addresses' => $addresses
        ]);
    }
    public function addfrontaddress()
    {
    return view('user.addfrontaddress');
    }
public function confirmBooking(Request $request)
{
    try {
        // Validate incoming request data
        $validatedData = $request->validate([
            'pandit_id' => 'required|exists:pandit_profile,id',
            'pooja_id' => 'required|exists:pooja_list,id',
            'pooja_fee' => 'required|numeric',
            'advance_fee' => 'required|numeric',
            'booking_date' => 'required|date',
            
            'address_id' => 'required',
            
        ]);

        // Assign the authenticated user's ID to the booking
        $validatedData['user_id'] = Auth::guard('users')->user()->userid;
        $validatedData['application_status'] = 'pending';
        $validatedData['payment_status'] = 'pending';
        $validatedData['pooja_status'] = 'pending';
        $validatedData['status'] = 'pending';
        // Create a new booking record
        $booking = Booking::create($validatedData);
        // $userHasPaid = Payment::where('user_id', $user_id)
        // ->where('booking_id', $booking->id)
        // ->exists();

        // // Set booking status based on payment status
        // if ($userHasPaid) {
        // $booking->status = 'approved'; // Assuming paid bookings are automatically approved
        // } else {
        // $booking->status = 'pending';
        // }

        // $booking->save();

        // Log success message
        \Log::info('Booking created successfully.', ['data' => $validatedData]);

        // Redirect to a success page or return a response
        return redirect()->route('booking.success', ['booking' => $booking->id])->with('success', 'Booking confirmed successfully!');
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error creating booking: ' . $e->getMessage());

        // Redirect back or return with an error message
        return back()->with('error', 'Failed to confirm booking. Please try again.');
    }
}


   
    // public function booknow(){
    //     $user = User::where('status', 'active')->first();
    //     $user_id = $user->userid;
    //     $addressdata = UserAddress::where('user_id', $user_id)->get();
    //     return view('user/booknow', compact('addressdata'));
    // }
    public function aboutus(){
        return view('user/aboutus');
    }
    public function contact(){
        return view('user/contact');
    }
    public function myprofile()
    {
        $user = Auth::guard('users')->user();
        $totalbookings = Booking::count();
        $totalCompleted = Booking::where('status', 'paid')
                                ->where('payment_status', 'paid')
                                ->where('application_status', 'approved')
                                ->where('pooja_status', 'completed')
                                ->count();
        $totalCanceled = Booking::where('status', 'canceled')
                                ->where('payment_status', 'refundprocess')
                                ->where('application_status', 'approved')
                                ->where('pooja_status', 'canceled')
                                ->count();
    
        // Fetch recent bookings for the user
        $bookings = Booking::with(['pooja.poojalist', 'pandit']) // Load relationship to get pooja details
                           ->leftJoin('payments', 'bookings.booking_id', '=', 'payments.booking_id')
                           ->where('bookings.user_id', $user->userid)
                           ->orderByDesc('bookings.created_at')
                           ->where('bookings.payment_status','!=' ,'paid')
                           ->select('bookings.*', 'payments.paid')
                           ->take(10) // Limit to 10 recent bookings (adjust as needed)
                           ->get();
    
        return view('user.my-profile', compact('bookings', 'totalbookings', 'totalCompleted', 'totalCanceled'));
    }
    
    public function orderhistory(Request $request)
    {
        $user = Auth::guard('users')->user();
        $filter = $request->input('filter', 'all'); // Default to 'all' if no filter is provided
    
        // Base query to fetch bookings for the user
        $bookingsQuery = Booking::with('pooja.poojalist', 'pandit', 'address','payment') // Load relationship to get pooja details
                                ->where('user_id', $user->userid)
                                ->orderByDesc('created_at');
    
        // Apply filter based on the status
        switch ($filter) {
            case 'pending':
                $bookingsQuery->where('status', 'pending')
                                ->where('payment_status', 'pending')
                                ->where('application_status', 'pending')
                                ->where('pooja_status', 'pending');
                break;
            case 'confirmed':
                $bookingsQuery->where('status', 'paid')
                                ->where('payment_status', 'paid')
                                ->where('application_status', 'approved')
                                ->where('pooja_status', 'pending');
                break;
            case 'completed':
                    $bookingsQuery->where('status', 'paid')
                                  ->where('payment_status', 'paid')
                                  ->where('application_status', 'approved')
                                  ->where('pooja_status', 'completed');
                    break;
            case 'canceled':
                $bookingsQuery->where('status', 'canceled')
                                ->where('payment_status', 'refundprocess')
                                ->where('application_status', 'approved')
                                ->where('pooja_status', 'canceled');
                break;
            case 'rejected':
                $bookingsQuery->where('status', 'rejected')
                                ->where('payment_status', 'rejected')
                                ->where('application_status', 'rejected')
                                ->where('pooja_status', 'rejected');
                break;
           
            default:
                // No filter applied, show all bookings with status 'paid' or 'rejected'
                $bookingsQuery->whereNotIn('status', ['pending'])
                            ->whereNotIn('payment_status',  ['pending'])
                            ->whereNotIn('application_status',  ['pending']);
                            // ->whereNotIn('pooja_status',  ['pending']);

                break;
        }
    
        // Get the bookings
        $bookings = $bookingsQuery->get();
    
        return view('user.orderhistory', compact('bookings'));
    }
    
    public function userprofile(){
        $user = Auth::guard('users')->user();
        return view('user/userprofile',compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'phonenumber' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            // 'dob' => 'nullable|date',
            'about' => 'nullable|string',
            'gender' => 'nullable|string',
            'userphoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::guard('users')->user();
        $user->name = $request->input('name');
        // $user->mobile_number = $request->input('phonenumber');
        $user->email = $request->input('email');
        // $user->dob = $request->input('dob');
        $user->about = $request->input('about');
        $user->gender = $request->input('gender');

        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($user->userphoto && Storage::exists($user->userphoto)) {
                Storage::delete($user->userphoto);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->userphoto = $avatarPath;
        }

        $user->save();

        return redirect()->route('user.userprofile')->with('success', 'Profile updated successfully.');
    }
    // public function deletePhoto()
    // {
    //     $user = Auth::guard('users')->user();

    //     if ($user->userphoto && Storage::exists('public/' . $user->userphoto)) {
    //         Storage::delete('public/' . $user->userphoto);
    //         $user->userphoto = null;
    //         $user->save();
    //     }

    //     return response()->json(['success' => 'Photo deleted successfully.']);
    // }
    // public function ratePooja($id)
    // {
    //     $booking = Booking::with('pooja', 'pandit')->findOrFail($id);

    //     return view('user/ratepooja', compact('booking'));
    // }


    // public function viewdetails(){
    //     return view('user/view-pooja-details');
    // }

    public function viewdetails($id)
    {
        $booking = Booking::with(['pooja.poojalist', 'pandit'])->findOrFail($id);
        return view('user/view-pooja-details', compact('booking'));
    }

    public function mngaddress(){
        $user = Auth::guard('users')->user();
        $addressdata = UserAddress::where('user_id', $user->userid)
                                    ->where('status','active')
                                    ->get();
        $addressdata->is_default = true;
        // $addressdata = UserAddress::where('userid', $user_id)->get();
        return view('user/mngaddress', compact('addressdata'));
    }
    public function setDefault($id)
    {
        $address = UserAddress::findOrFail($id);

        // Ensure the address belongs to the authenticated user
        if ($address->user_id != Auth::guard('users')->user()->userid) {
            return redirect()->back()->with('error', 'You do not have permission to set this address as default.');
        }

        // Set all addresses to not default
        UserAddress::where('user_id', $address->user_id)
            ->update(['default' => '0']);

        // Set the selected address as default
        $address->default = '1';
        $address->save();

        return redirect()->back()->with('success', 'Address set as default successfully.');
    }


    public function addaddress(){
        return view('user/add-address');
    }

    public function saveaddress(Request $request)
    {
        $user = Auth::guard('users')->user();
        $userid = $user->userid;
    
        // Check if the user already has addresses
        $hasAddresses = UserAddress::where('user_id', $userid)
                                    ->where('status', 'active')->exists();
    
        // Create the new address
        $addressdata = new UserAddress();
        $addressdata->user_id = $userid;
        $addressdata->country = $request->country;
        $addressdata->state = $request->state;
        $addressdata->city = $request->city;
        $addressdata->pincode = $request->pincode;
        $addressdata->area = $request->area;
        $addressdata->address_type = $request->address_type;
        $addressdata->status = 'active';
    
        // Set as default if it's the first address
        if (!$hasAddresses) {
            $addressdata->default = 1;
        }
    
        $addressdata->save();
    
        return redirect()->route('mngaddress')->with('success', 'Address created successfully.');
    }
    
    public function savefrontaddress(Request $request)
    {
        $user = Auth::guard('users')->user();
        $userid = $user->userid;
    
        // Check if the user already has addresses
        $hasAddresses = UserAddress::where('user_id', $userid)->where('status', 'active')->exists();
    
        // Create the new address
        $addressdata = new UserAddress();
        $addressdata->user_id = $userid;
        $addressdata->country = $request->country;
        $addressdata->state = $request->state;
        $addressdata->city = $request->city;
        $addressdata->pincode = $request->pincode;
        $addressdata->area = $request->area;
        $addressdata->address_type = $request->address_type;
        $addressdata->status = 'active';
    
        // Set as default if it's the first address
        if (!$hasAddresses) {
            $addressdata->default = 1;
        }
    
        $addressdata->save();
    
        return redirect()->back()->with('success', 'Address created successfully.');
    }
    
    public function removeAddress($id)
    {
        // Find the address by ID
        $address = UserAddress::find($id);
    
        if ($address) {
            // Ensure the address belongs to the authenticated user
            if ($address->user_id != Auth::guard('users')->user()->userid) {
                return redirect()->back()->with('error', 'You do not have permission to remove this address.');
            }
    
            // Check if the address is the default address
            if ($address->default == '1') {
                // Check if there are other addresses
                $otherAddresses = UserAddress::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->count();
    
                if ($otherAddresses > 0) {
                    // Set a new default address if other addresses exist
                    $newDefaultAddress = UserAddress::where('user_id', $address->user_id)
                        ->where('id', '!=', $address->id)
                        ->first();
    
                    if ($newDefaultAddress) {
                        $newDefaultAddress->default = '1';
                        $newDefaultAddress->save();
                    }
                } else {
                    return redirect()->back()->with('error', 'You cannot delete the default address unless another address is set as default.');
                }
            }
    
            // Set the address status to 'inactive'
            $address->status = 'inactive';
            $address->save();
    
            return redirect()->back()->with('error', 'Address Deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Address not found.');
        }
    }
    
    public function editAddress($id)
    {
        $address = UserAddress::find($id);
        return view('user/edit_address', compact('address'));
    }
    public function updateAddress(Request $request)
    {
        $address = UserAddress::find($request->id);

        if ($address) {
            // $address->fullname = $request->fullname;
            // $address->number = $request->number;
            $address->country = $request->country;
            $address->state = $request->state;
            $address->city = $request->city;
            $address->pincode = $request->pincode;
            $address->area = $request->area;
            $address->address_type = $request->address_type;
            $address->status = 'active';
            $address->save();

            return redirect()->route('mngaddress')->with('success', 'Address updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Address not found.');
        }
    }

    public function coupons(){
        return view('user/coupons');
    }


    public function ratePooja($id)
    {
        $booking = Booking::findOrFail($id); // Ensure the booking exists
    
        // Check if the user has already rated this booking
        $rating = Rating::where('booking_id', $id)
                        ->where('user_id', Auth::guard('users')->user()->userid)
                        ->first();
    
        return view('user/ratepooja', compact('booking', 'rating'));
    }

    public function submitOrUpdateRating(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|between:1,5',
            'feedback_message' => 'nullable|string',
            'audioFile' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'rating_id' => 'nullable|exists:ratings,id', // For updating an existing rating
        ]);

        // Determine if this is a new rating or an update
        $rating = $request->has('rating_id') 
            ? Rating::findOrFail($request->rating_id) 
            : new Rating();

        // Fill rating details
        $rating->user_id = Auth::guard('users')->user()->userid; // Save the authenticated user's ID
        $rating->booking_id = $validatedData['booking_id'];
        $rating->rating = $validatedData['rating'];
        $rating->feedback_message = $validatedData['feedback_message'];

        // Handle audio file upload
        if ($request->hasFile('audioFile')) {
            // Delete old audio file if exists
            if ($rating->audio_file) {
                Storage::disk('public')->delete($rating->audio_file);
            }
            $audioPath = $request->file('audioFile')->store('audio', 'public');
            $rating->audio_file = $audioPath;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($rating->image_path) {
                Storage::disk('public')->delete($rating->image_path);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $rating->image_path = $imagePath;
        }

        $rating->save();

        return redirect()->route('orderhistory')
                        ->with('success', 'Rating submitted successfully!')
                        ->with('rating', $rating);
    }

    
    
    

    public function deletePhoto()
    {
        $user = Auth::guard('users')->user();
        
        // Log the user ID attempting to delete photo
        \Log::info('User ID ' . $user->userid . ' is attempting to delete their photo.');

        if ($user->userphoto) {
            try {
                // Delete the photo from storage
                Storage::delete('public/' . $user->userphoto);

                // Update user's photo column in the database (if necessary)
                $user->update(['userphoto' => null]);

                // Log success message
                Log::info('Photo deleted successfully for User ID ' . $user->userid);

                return response()->json(['message' => 'Photo deleted successfully'], 200);
            } catch (\Exception $e) {
                // Log error if deletion fails
                Log::error('Failed to delete photo for User ID ' . $user->userid . ': ' . $e->getMessage());

                return response()->json(['message' => 'Failed to delete photo'], 500);
            }
        }

        // Log if no photo found for deletion
        \Log::info('No photo found for deletion for User ID ' . $user->id);
        return response()->json(['message' => 'No photo found for deletion'], 404);
    }

    public function fetchPoojas(Request $request)
    {
        try {
            $query = $request->input('query');

            // Fetch poojas from database based on search query
            $poojas = PoojaList::where('pooja_name', 'like', '%' . $query . '%')->limit(10)->get();

            return response()->json($poojas);
        } catch (\Exception $e) {
            // Log the error for further investigation
            \Log::error('Error fetching poojas: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
