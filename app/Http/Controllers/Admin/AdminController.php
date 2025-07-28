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
use App\Models\ProductOrder;
use App\Models\ProductRequest;
use App\Models\ProductSucription;
use Illuminate\Http\Request;
use App\Models\PanditEducation;
use App\Models\UserAddress;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    //
    public function adminlogin()
    {

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

        $newUserSubscription = Subscription::whereDate('created_at', Carbon::today())
        ->distinct('user_id')
        ->where('status', 'pending')                   
            ->count('user_id');

        $nonAssignedRidersCount = Subscription::with('relatedOrder')
        ->where('status', 'active')
        ->whereHas('relatedOrder', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('rider_id')
                    ->orWhere('rider_id', '');
            });
        })
        ->count();
        
        $renewSubscription = Subscription::whereDate('created_at', Carbon::today()) // Check rows created today
        ->whereIn('order_id', function ($query) {
            $query->select('order_id')
                ->from('subscriptions')
                ->groupBy('order_id')
                ->havingRaw('COUNT(order_id) > 1'); // Find duplicate order IDs
        })
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

        $tomorrow = Carbon::tomorrow()->toDateString();

        $nextDayPaused = Subscription::where('status', 'active')
            ->whereDate('pause_start_date', $tomorrow)
            ->count();

        // fetch the expired subscriptions whose new subscription is not created ( expired )
        $expiredSubscriptions = Subscription::where('status', 'expired')
        ->whereNotIn('user_id', function ($query) {
            $query->select('user_id')
                ->from('subscriptions')
                ->whereIn('status', ['active', 'paused', 'resume']);
        })
        ->distinct('user_id')
        ->latest('end_date')
        ->count();

        $currentUser = $activeSubscriptions + $pausedSubscriptions + $expiredSubscriptions;

        $todayEndSubscription = Subscription::where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNotNull('new_date') // Check if new_date is available
                            ->whereDate('new_date', Carbon::today()); // Count using new_date if available
                })
                ->orWhere(function ($subQuery) {
                    $subQuery->whereNull('new_date') // Check if new_date is not available
                            ->whereDate('end_date', Carbon::today()); // Count using end_date
                });
            })
            ->where('status', 'active') // Status must be active
            ->count();
        
            $riders = RiderDetails::where('status','active')->get();

        // Fetch dynamic data for each rider
        $ridersData = $riders->map(function ($rider) {
            // Total assigned orders to this rider
            $totalAssignedOrders = Order::where('rider_id', $rider->rider_id) // Filter by rider_id
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active'); // Check subscription status
            })
            ->count();

            // Total delivered orders today by this rider
            $totalDeliveredToday = DeliveryHistory::whereDate('created_at', Carbon::today())
                ->where('rider_id', $rider->rider_id)
                ->where('delivery_status', 'delivered')
                ->count();

            return [
                'rider' => $rider,
                'totalAssignedOrders' => $totalAssignedOrders,
                'totalDeliveredToday' => $totalDeliveredToday,
            ];
        });

        // Total Riders
        $totalRiders = RiderDetails::where('status','active')->count();

        // Total Deliveries This Month
        $totalDeliveriesThisMonth = DeliveryHistory::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('delivery_status', 'delivered')
            ->count();
            // Total Deliveries Today
            $totalDeliveriesToday = DeliveryHistory::whereDate('created_at', now()->toDateString())->where('delivery_status', 'delivered')
            ->count();

        // Total Deliveries
        $totalDeliveries = DeliveryHistory::where('delivery_status', 'delivered')->count();
    
        //Total Expenses in a Day
        $totalExpensesday = FlowerPickupDetails::whereDate('pickup_date', Carbon::today())->sum('total_price');
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
        
        // Calculate the total price from the flower_pickup_details table ( total expenses )
        $totalFlowerPickupPrice = FlowerPickupDetails::sum('total_price');
        
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

            // Count total podcasts with status 'active'
            $totalActivePodcasts = PublishPodcast::where('status', 'active')->count();
            $totalCompletedScripts = PodcastPrepair::where('podcast_script_status', 'COMPLETED')->count();
            $totalCompletedRecoding = PodcastPrepair::where('podcast_recording_status', 'COMPLETED')->count();
            $totalCompletedEditing = PodcastPrepair::where('podcast_editing_status', 'COMPLETED')->count();

            return view('admin/dashboard', compact(
                'ridersData',
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
                'currentUser',
                'pausedSubscriptions',
                'nextDayPaused',
                'expiredSubscriptions',
                'todayEndSubscription',
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
                'nonAssignedRidersCount'
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

    public function acceptPandit($id)
    {
        $profile = Profile::find($id);
        if ($profile) {
            $profile->pandit_status = 'accepted';
        }
        if ($profile->save()) {
            return redirect()->back()->with('success', 'Pandit Id Activate.');
        } 
    }

    public function rejectPandit($id)
    {
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

    public function panditprofile()
    {
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

    public function addCareer()
    {
        return view('admin/add-career');
    }
  
    public function manageuser()
    {
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

    public function showRiderDetails($riderId)
    {
        // Fetch rider details
        $rider = RiderDetails::where('rider_id', $riderId)->firstOrFail();

        // Fetch all riders except the current one
        $allRiders = RiderDetails::where('rider_id', '!=', $riderId)->get();

        // Fetch orders assigned to this rider where subscription is active
        $orders = Order::where('rider_id', $riderId)
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['flowerProduct', 'user', 'subscription'])
            ->get();

        // Fetch today's delivery history for this rider
        $today = Carbon::today();

        $deliveryHistory = DeliveryHistory::where('rider_id', $riderId)
            ->whereDate('created_at', $today)
            ->with('order.user')
            ->get();

        return view('admin.delivery-assign', compact('rider', 'orders', 'allRiders', 'deliveryHistory'));
    }

    public function transferOrders(Request $request)
    {
        // Validate the request
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,order_id',
            'new_rider_id' => 'required|exists:flower__rider_details,rider_id',
        ]);

        try {
            // Update the rider_id for all selected orders
            Order::whereIn('order_id', $request->order_ids)
                ->update(['rider_id' => $request->new_rider_id]);

            return redirect()->back()->with('success', 'Orders transferred successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to transfer orders: ' . $e->getMessage());
        }
    }

   public function showAddressByCategory()
    {
        $categories = ['apartment', 'individual', 'temple', 'business'];

        $addressCounts = [];

        foreach ($categories as $category) {
            $addressCounts[$category] = UserAddress::where('place_category', $category)->count();
        }

        return view('admin.address-category-summary', compact('addressCounts'));
    }

 public function getAddressUsersByCategory(Request $request)
{
    $category = $request->input('category');

    $addresses = \App\Models\UserAddress::with(['user.orders.rider'])
        ->where('place_category', $category)
        ->get();

    $result = $addresses->map(function ($address) {
        $user = $address->user;
        $riderName = '—';

        if ($user && $user->orders->count()) {
            foreach ($user->orders as $order) {
                if ($order->rider) {
                    $riderName = $order->rider->rider_name ?? '—';
                    break;
                }
            }
        }

        return [
            'user_id' => $user?->userid,
            'address_id' => $address->id,
            'name' => $user?->name ?? '—',
            'mobile_number' => $user?->mobile_number ?? '—',
            'apartment_name' => $address->apartment_name ?? '—',
            'apartment_flat_plot' => $address->apartment_flat_plot ?? '—',
            'rider_name' => $riderName,
        ];
    });

    return response()->json($result);
}

public function updateAddress(Request $request)
{
    $request->validate([
        'address_id' => 'required|exists:user_addresses,id',
        'user_id' => 'required|exists:users,userid',
        'name' => 'required|string|max:255',
        'apartment_name' => 'required|string|max:255',
        'apartment_flat_plot' => 'required|string|max:255',
    ]);

    // Update user name
    $user = \App\Models\User::where('userid', $request->user_id)->first();
    if ($user) {
        $user->update([
            'name' => $request->name,
        ]);
    }

    // Update address
    $address = \App\Models\UserAddress::findOrFail($request->address_id);
    $address->update([
        'apartment_name' => $request->apartment_name,
        'apartment_flat_plot' => $request->apartment_flat_plot,
    ]);

    return response()->json(['message' => 'Customer and address updated successfully.']);
}



}