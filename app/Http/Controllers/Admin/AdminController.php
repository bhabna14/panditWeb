<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Career;
use App\Models\Profile;
use App\Models\EduDetail;
use App\Models\PanditVedic;
use App\Models\VedicDetail;
use App\Models\IdcardDetail;
use App\Models\PanditIdCard;
use App\Models\Booking;
use App\Models\Poojadetails;
use App\Models\Poojaitems;
use App\Models\PanditDevice;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\PublishPodcast;
use App\Models\FlowerPickupDetails;
use App\Models\RiderDetails;
use App\Models\DeliveryHistory;
use App\Models\PodcastPrepair;
use App\Models\FlowerRequest;
use App\Models\UserDevice;
use App\Models\PanditLogin;
use App\Models\Bankdetail;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\PanditEducation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    //
    public function adminlogin(){

        return view("adminlogin");
    }
    public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required',
        'password' => 'required'
    ]);

    if (Auth::guard('admins')->attempt($credentials)) {
        $admin = Auth::guard('admins')->user();
        session(['admin_role' => $admin->role]); // Store role in session
        
        return redirect()->intended('/admin/dashboard');
    } else {
        return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid email or password']);
    }
}

public function admindashboard()
{
    // Fetch the total number of pandits and pending pandits
    $totalPandit = Profile::where('status', 'active')->count();

    // Count the total number of pandits and pending pandits
    $pendingPandit = Profile::where('pandit_status', 'pending')->count();

    // Fetch the total number of orders and users
    $totalOrder = Booking::count();

    // Fetch the total number of orders and users
    $totalUser = User::count();

    // Fetch the total number of pandits and pending pandits
    $pandit_profiles = Profile::orderBy('id', 'desc')
                                ->where('pandit_status', 'pending')                        
                                ->get(); // Fetch all profiles      
    
    // Fetch the total number of notifications (unread) (latest first)(not required now)
    $notifications = Notification::where('is_read', false)->latest()->get();  
    


    // Fetch the total number of new user subscriptions today
    $newUserSubscription = Order::whereDate('created_at', Carbon::today())
        ->whereNull('request_id') // Add condition for request_id to be NULL
        ->whereNotIn('user_id', function ($query) {
            $query->select('user_id')
                ->from('orders')
                ->whereNull('request_id') // Ensure request_id is NULL in the subquery
                ->whereDate('created_at', '<', Carbon::today());
        })
        ->count();
    
    // Fetch the total number of renewed user subscriptions today
    $renewSubscription = Order::whereDate('created_at', Carbon::today())
    ->whereNull('request_id')
    ->whereIn('user_id', function ($query) {
        $query->select('user_id')
            ->from('orders')
            ->whereNull('request_id')
            ->whereDate('created_at', '<', Carbon::today());
    })
    ->distinct('user_id')
    ->count();

    // Fetch the total number of subscription orders requested today ( not used in dashboard )
    $subscriptionOrderToday = Order::whereDate('created_at', Carbon::today())
                                ->whereNull('request_id')
                                ->count();

    // Fetch the total number of flower requests requested today (customized order)
    $ordersRequestedToday = FlowerRequest::whereDate('created_at', Carbon::today())->count();

    // Fetch the total number of active subscriptions
    $activeSubscriptions = Subscription::where('status', 'active')->count();

    // Fetch the total number of paused subscriptions
    $pausedSubscriptions = Subscription::where('status', 'paused')->count();

    // fetch the expired subscriptions whose new subscription is not created ( expired )
    $expiredSubscriptions = Subscription::where('status', 'expired')
        ->whereNotIn('user_id', function ($query) {
            $query->select('user_id')
                ->from('subscriptions')
                ->where('status', 'active');
        })
        ->count();
    // Individual Rider Details
    //display to total assigned orders to rider from orders table whose request_id is null  where rider_id is RIDER73783 and add subscription status is active from subcription table 
    $totalAssignedOrderstobablu = Order::join('subscriptions', 'orders.order_id', '=', 'subscriptions.order_id')
    ->whereNull('orders.request_id')  // Ensure request_id is null
    ->where('orders.rider_id', 'RIDER73783')  // Filter by rider_id
    ->where('subscriptions.status', 'active')  // Ensure the subscription status is active
    ->count();
    // total delivered in today use the table devliery history and withe the condition of this rider_id (RIDER73783) and created_at date is today
    $totalDeliveredTodaybybablu = DeliveryHistory::whereDate('created_at', Carbon::today())
    ->where('rider_id', 'RIDER73783')
    ->where('delivery_status', 'delivered')
    ->count();


    $totalAssignedOrderstosubrat = Order::join('subscriptions', 'orders.order_id', '=', 'subscriptions.order_id')
    ->whereNull('orders.request_id')  // Ensure request_id is null
    ->where('orders.rider_id', 'RIDER87967')  // Filter by rider_id
    ->where('subscriptions.status', 'active')  // Ensure the subscription status is active
    ->count();
    // total delivered in today use the table devliery history and withe the condition of this rider_id (RIDER87967) and created_at date is today
    $totalDeliveredTodaybysubrat = DeliveryHistory::whereDate('created_at', Carbon::today())
    ->where('rider_id', 'RIDER87967')
    ->where('delivery_status', 'delivered')
    ->count();


    $totalAssignedOrderstoprahlad = Order::join('subscriptions', 'orders.order_id', '=', 'subscriptions.order_id')
    ->whereNull('orders.request_id')  // Ensure request_id is null
    ->where('orders.rider_id', 'RIDER91711')  // Filter by rider_id
    ->where('subscriptions.status', 'active')  // Ensure the subscription status is active
    ->count();
    // total delivered in today use the table devliery history and withe the condition of this rider_id (RIDER91711) and created_at date is today
    $totalDeliveredTodaybyprahlad = DeliveryHistory::whereDate('created_at', Carbon::today())
    ->where('rider_id', 'RIDER91711')
    ->where('delivery_status', 'delivered')
    ->count();
    //Rider Details
    // Total Riders
    $totalRiders = RiderDetails::count();

    // Total Deliveries This Month
    $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
        // Total Deliveries Today
        $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())
        ->count();

    // Total Deliveries
    $totalDeliveries = DeliveryHistory::count();
  
    //Expenses Details in a Day
    //Total Expenses in a Day
    $totalExpensesday = FlowerPickupDetails::whereDate('created_at', Carbon::today())->sum('total_price');
    // total paid expenses in a day
    $totalPaidExpensesday = FlowerPickupDetails::whereDate('created_at', Carbon::today())
        ->where('payment_status', 'Paid')
        ->sum('total_price');
    // total unpaid expenses in a day
    $totalUnpaidExpensesday = FlowerPickupDetails::whereDate('created_at', Carbon::today())
        ->where('payment_status', 'Pending')
        ->sum('total_price');

    //Expenses Details in The month
    // Total Amount Paid This Month
   $totalPaidThisMonth = FlowerPickupDetails::where('payment_status', 'Paid')
   ->whereYear('created_at', now()->year)
   ->whereMonth('created_at', now()->month)
   ->sum('total_price');

    // Total Amount Unpaid This Month
    $totalUnpaidThisMonth = FlowerPickupDetails::where('payment_status', 'Pending')
    ->whereYear('created_at', now()->year)
    ->whereMonth('created_at', now()->month)
    ->sum('total_price');

    // Total Amount This Month (No Status Condition)
    $totalAmountThisMonth = FlowerPickupDetails::whereYear('created_at', now()->year)
    ->whereMonth('created_at', now()->month)
    ->sum('total_price');

      
    // Total details of flower orders
    // Calculate the total price from the flower_pickup_details table ( total expenses )
    $totalFlowerPickupPrice = FlowerPickupDetails::sum('total_price');

    // Calculate the total price for orders without request_id (total income of subscription orders)
    $ordersWithoutRequestId = Order::whereNull('request_id')
            ->get();
    
    $totalPriceWithoutRequestId = 0;
    foreach ($ordersWithoutRequestId as $order) {
        $payment = $order->flowerPayments()->where('payment_status', 'paid')->first();
        if ($payment) {
            $totalPriceWithoutRequestId += $order->total_price;
        }
    }

    // Calculate the total price for orders with request_id ( total income of customized orders )
    $ordersWithRequestId = Order::whereNotNull('request_id')
        ->get();

    $totalPriceWithRequestId = 0;
    foreach ($ordersWithRequestId as $order) {
        $payment = $order->flowerPayments()->where('payment_status', 'paid')->first();
        if ($payment) {
            $totalPriceWithRequestId += $order->total_price;
        }
    }

    // Podcast Details
    // Count total podcasts with status 'active'
    $totalActivePodcasts = PublishPodcast::where('status', 'active')->count();
    // Total Scripts Completed
    $totalCompletedScripts = PodcastPrepair::where('podcast_script_status', 'COMPLETED')->count();
    $totalCompletedRecoding = PodcastPrepair::where('podcast_recording_status', 'COMPLETED')->count();
    $totalCompletedEditing = PodcastPrepair::where('podcast_editing_status', 'COMPLETED')->count();


    return view('admin/dashboard', compact(
        'totalAssignedOrderstoprahlad',
        'totalDeliveredTodaybyprahlad',
        'totalDeliveredTodaybysubrat',
        'totalAssignedOrderstosubrat',
        'totalDeliveredTodaybybablu',
        'totalAssignedOrderstobablu',
        'renewSubscription' ,
        'newUserSubscription',
        'totalCompletedEditing',
        'totalCompletedRecoding',
        'totalCompletedScripts',
        'totalPaidThisMonth' ,
        'totalUnpaidThisMonth',
        'totalAmountThisMonth',
        'totalRiders' ,
        'totalDeliveriesThisMonth',
        'totalDeliveriesToday',
        'totalDeliveries' ,
        'totalActivePodcasts',
        'totalFlowerPickupPrice',
        'activeSubscriptions',
        'pausedSubscriptions',
        'expiredSubscriptions',
        'ordersRequestedToday',
        'subscriptionOrderToday',
        'notifications',
        'pandit_profiles',
        'totalPandit',
        'pendingPandit',
        'totalOrder',
        'totalUser',
        'totalPriceWithoutRequestId',
        'totalPriceWithRequestId',
        'totalExpensesday',
        'totalPaidExpensesday',
        'totalUnpaidExpensesday',
    ));
}

    public function adminlogout()
    {
      
        return view("adminlogin");
    }

    public function managepandit() {
        $pandit_profiles = Profile::orderBy('id', 'desc')->get(); // Fetch all profiles
        return view('admin/managepandit', compact('pandit_profiles'));
    }

    public function showProfile($id) {

        $pandit_profile = Profile::find($id);
        $pandtId = $pandit_profile->pandit_id;

        $pandit_careers = Career::where('pandit_id', $pandtId)->where('status','active')->get();
        $pandit_idcards = PanditIdCard::where('pandit_id', $pandtId)->where('status','active')->get();
        $pandit_vedics = PanditVedic::where('pandit_id', $pandtId)->where('status','active')->get();
        $pandit_educations = PanditEducation::where('pandit_id', $pandtId)->where('status','active')->get();

        $pandit_login_detail = PanditLogin::where('pandit_id', $pandtId)->first();
        $pandit_bankdetails = Bankdetail::where('pandit_id', $pandtId)->get();

        $single_pandit = Profile::where('pandit_id', $pandtId)->firstOrFail();
 
        // Fetch the related pooja details for this pandit
        $pandit_pujas = Poojadetails::where('pandit_id', $single_pandit->pandit_id)
            ->where('status','active')
            ->with('poojalist') // Load the poojalist relationship
            ->get();
        // Fetch the samagri items separately from the Poojaitems table
        $samagri_items = Poojaitems::where('pandit_id', $single_pandit->pandit_id)
            ->where('status','active')
            ->with(['item', 'variant']) // Load the related pooja and variant
            ->get();

        $pandit_logins = PanditDevice::where('pandit_id', $pandtId)->get();
        return view('admin/pandit-profile', compact('pandit_profile','pandit_careers','pandit_idcards','pandit_vedics','pandit_educations','pandit_pujas','samagri_items','pandit_logins','pandit_login_detail','pandit_bankdetails'));

    }

    public function deletIdproof($id)
    {
            $affected = PanditIdCard::where('id', $id)->update(['status' => 'deleted']);
                        
            if ($affected) {
                return redirect()->back()->with('success', 'Data delete successfully.');
            } else {
                return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
            }
      
        }
        public function deletEducation($id)
        {
                $affected = PanditEducation::where('id', $id)->update(['status' => 'deleted']);
                            
                if ($affected) {
                    return redirect()->back()->with('success', 'Data delete successfully.');
                } else {
                    return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
                }
          
            }
            public function deletVedic($id)
            {
                    $affected = PanditVedic::where('id', $id)->update(['status' => 'deleted']);
                                
                    if ($affected) {
                        return redirect()->back()->with('success', 'Data delete successfully.');
                    } else {
                        return redirect()->back()->with('danger', 'Data delete unsuccessfully.');
                    }
              
                }

    public function acceptPandit($id) {
        $profile = Profile::find($id);
        if ($profile) {
            $profile->pandit_status = 'accepted';
        }
        if ($profile->save()) {
            return redirect()->back()->with('success', 'Pandit Id Activate.');
        } 
    }

    public function rejectPandit($id) {
        $profile = Profile::find($id);
        if ($profile) {
            $profile->pandit_status = 'rejected';
            $profile->save();
        }
        if ($profile->save()) {
            return redirect()->back()->with('success', 'Pandit Id Deactivate.');
        } 
        }

        public function addProfile()
        {
            $languages = [
                'English','Odia','Hindi','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
                'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
                'Sanskrit', 'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
            ];
            return view('admin/add-profile',compact('languages'));
        }

    public function panditprofile(){
        return view('admin/pandit-profile');
    }
    public function saveprofile(Request $request)
    {
        $request->validate([
            // 'profile_photo' => 'nullable|image|max:2048', 
            // 'qualification' => 'required|string|max:255',
            // 'experience' => 'required|integer|min:0',
            // 'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
            // 'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            // 'education_type.*' => 'required|string|in:10th,+2,+3,Master Degree',
            // 'upload_edu.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            // 'vedic_type.*' => 'required|string|max:255',
            // 'upload_vedic.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $profile = new Profile();

        $profile->profile_id = $request->profile_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->slug = Str::slug($request->name, '-');
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;

        $pandilang = $request->input('language');
 
            $langString = implode(',', $pandilang);
            $profile->language = $langString;

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/profile_photo/' . $filename;
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filePath;
        }
// add career information

$career = new Career();

$career->pandit_id = $request->profile_id;
$career->qualification = $request->qualification;
$career->total_experience = $request->experience;

 // Pandit Career Photo Upload

 foreach ($request->id_type as $key => $id_type) {
    $file = $request->file('upload_id')[$key];
    $fileName = time().'_'.$file->getClientOriginalName();
    $filePath = $file->move(public_path('uploads/id_proof'), $fileName);

    // Save form data to the database
    $iddata = new IdcardDetail();
    $iddata->pandit_id = $request->profile_id;
    $iddata->id_type =  $id_type;
    $iddata->upload_id = $fileName; // Save file path in the database
    $iddata->save();
}

//Pandit Education Photo Upload

foreach ($request->education_type as $key => $education_type) {
    $file = $request->file('upload_edu')[$key];

    $fileName = time().'_'.$file->getClientOriginalName();
    $filePath = $file->move(public_path('uploads/edu_details'), $fileName);

    // Save form data to the database
    $edudata = new EduDetail();
    $edudata->pandit_id = $request->profile_id;
    $edudata->education_type = $education_type;
    $edudata->upload_education = $fileName; // Save file path in the database
    $edudata->save();
}

// Pandit Vedic Photo Upload


foreach ($request->vedic_type as $key => $vedic_type) {
    $file = $request->file('upload_vedic')[$key];

    $fileName = time().'_'.$file->getClientOriginalName();
    $filePath = $file->move(public_path('uploads/vedic_details'), $fileName);

    // Save form data to the database
    $vedicdata = new VedicDetail();
    $vedicdata->pandit_id = $request->profile_id;
    $vedicdata->vedic_type = $vedic_type;
    $vedicdata->upload_vedic = $fileName; // Save file path in the database
    $vedicdata->save();
}

$profileSaved = $profile->save();
$careerSaved = $career->save();

if ($profileSaved && $careerSaved) {
    return redirect()->back()->with('success', 'Data saved successfully.');
} else {
    return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
}
    }
    public function addCareer(){
        return view('admin/add-career');
    }
  
    public function manageuser(){
        $users = User::all(); // Fetch all users using Eloquent
        
        return view('admin/manageuser', compact('users'));
    }
    public function userProfile($id)
    {
        // Fetch the user and their bookings
        $user = User::findOrFail($id);
        // dd();
        $bookings = Booking::with(['pooja', 'pandit', 'address', 'user', 'poojaStatus', 'ratings'])
                            ->where('user_id', $user->userid)->get();
        $user_logins = UserDevice::where('user_id', $user->userid)->get();
        // Pass the data to the view
        return view('admin.user-profile', compact('user', 'bookings','user_logins'));
    }
    
   
}