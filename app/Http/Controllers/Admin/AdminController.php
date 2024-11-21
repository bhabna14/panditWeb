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
use App\Models\UserDevice;
use App\Models\PanditLogin;
use App\Models\Bankdetail;

use Illuminate\Http\Request;
use App\Models\PanditEducation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
        // $credentials = $request->only('name', 'password');
        if (Auth::guard('admins')->attempt($request->only('email', 'password'))) {
           
            return redirect()->intended('/admin/dashboard');
            
        }
    
       else {
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number or email']);
        }
    }

    public function admindashboard()
    {
        $totalPandit = Profile::where('status', 'active')->count();
        $pendingPandit = Profile::where('pandit_status', 'pending')->count();
        $totalOrder = Booking:: count();
        $totalUser = User::count();

        $pandit_profiles = Profile::orderBy('id', 'desc')
                                    ->where('pandit_status', 'pending')                        
                                    ->get(); // Fetch all profiles                  
         return view('admin/dashboard', compact('pandit_profiles','totalPandit','pendingPandit','totalOrder','totalUser'));
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