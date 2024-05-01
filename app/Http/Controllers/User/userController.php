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
use PDF;
use DB;
use Illuminate\Support\Facades\Hash;


class userController extends Controller
{
    //

    public function userlogin(){
        return view("login");
    }
    public function userauthenticate(Request $request)
    {
// dd($request);
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


    public function dashboard(){
        return view('user.dashboard');
    }
    public function userlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

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
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'phonenumber' => 'required',
            'password' => 'required|min:8'
        ]);



        $user = new User();
        $mobileNumberExists = User::where('phonenumber', $request->phonenumber)->exists();
        if($mobileNumberExists){
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Phone Number is already exist.']);

        }else{
            if ($request->has('first_name')) {
                $user->first_name = $request->first_name;
            }
            $user->userid = $request->userid;
            $user->name = $request->first_name;
            $user->last_name = $request->last_name;

            $user->phonenumber = $request->phonenumber;
            $user->email = $request->phonenumber;
            $user->password = Hash::make($request->password);
            $user->role = 'user';
            $user->status = 'active';
            $user->application_status = 'pending';
            $user->added_by = 'user';
            $user->otp = '234234';

            // Save the user
            $user->save();

        
            return redirect('/')->with('success', 'Registered successfully.');
        }

      
    }

    public function sebayatregister(){

        // $userid = Auth::user()->userid;
        $userid = Auth::guard('users')->user()->userid;
      
        $userinfo = User::where('userid', $userid)->first();
        $bankinfo = Bankdetail::where('userid', $userid)->first();
        $childinfos = Childrendetail::where('userid', $userid)
                                            ->where('status','active')->get();
        $iddetails = IdcardDetail::where('userid', $userid) ->where('status','active')->get();
        $address = Addressdetail::where('userid', $userid)->first();
        $bankinfo = Bankdetail::where('userid', $userid)->first();
        
               
                return view('user.sebayatregister',compact('userinfo','bankinfo','childinfos','iddetails','address','bankinfo'));
        // return view('user.sebayatregister', ['user' => $user]);
        
        // return view('user.sebayatregister');
    }


    public function updateuserinfo(Request $request,$id){
       
        // Retrieve the user record
    
        $userdata = User::find($id);
         if($request->hasFile('userphoto')){
 
             $path = 'assets/uploads/userphoto/'.$userdata->userphoto;
         
         
             $file = $request->file('userphoto');
             $ext = $file->getClientOriginalExtension();
             $filename = time().'.'.$ext;
             $file->move('assets/uploads/userphoto/',$filename);
             $userdata->userphoto=$filename;
         }
        $userdata->userid = $request->userid;
        $userdata->name = $request->input('first_name');
        $userdata->first_name = $request->input('first_name');
        $userdata->last_name = $request->last_name;
        $userdata->email  = $request->email ;
        $userdata->phonenumber = $request->phonenumber;
        $userdata->bloodgrp = $request->bloodgrp;
        $userdata->dob = $request->dob;
        $userdata->update();
 
 
        return redirect()->back()->with('success', 'User updated successfully');
     }
     public function updateFamilyInfo(Request $request,$id){
             $userdata = User::find($id);
         $userdata->userid = $request->userid;
 
             $userdata->fathername = $request->fathername;
             $userdata->mothername = $request->mothername;
 
             $userdata->marital = $request->marital;
             $userdata->spouse  = $request->spouse ;
             $userdata->update();
            
         return redirect()->back()->with('success', 'Family Info updated successfully');
 
 
     }
     public function updateChildInfo(Request $request){
     
         
         foreach ($request->childrenname as $key => $childrenname) {
             $dob = $request->dob[$key];
             $gender = $request->gender[$key];
 
             
             // Save form data to the database
             $childata = new Childrendetail();
             $childata->userid = $request->userid;
             $childata->childrenname =  $childrenname;
 
             $childata->dob =  $dob;
             $childata->gender =  $gender;
             $childata->status =  "active";
 
             $childata->save();
         }
          return redirect()->back()->with('success', 'Child Info updated successfully');
 
 
     }
     public function updatechildstatus($id)
     {
         $affected = Childrendetail::where('id', $id)
                         ->update(['status' => 'deleted']);
 
             return redirect()->back()->with('success', 'Data delete successfully.');
     
     }
 
     public function updateIdInfo(Request $request){
         foreach ($request->idproof as $key => $idproof) {
             $idnumber = $request->idnumber[$key];
             $file = $request->file('uploadoc')[$key];
             
             // Handle file upload
             // $filePath = $file->storeAs('public', $file->getClientOriginalName());
 
             $fileName = time().'_'.$file->getClientOriginalName();
             $filePath = $file->move(public_path('uploads'), $fileName);
             // Save form data to the database
             $iddata = new IdcardDetail();
             $iddata->userid = $request->userid;
             $iddata->idproof =  $idproof;
             $iddata->idnumber =  $idnumber;
             $iddata->uploadoc = 'uploads/'.$fileName; // Save file path in the database
             $iddata->status =  "active";
 
             $iddata->save();
         }
        return redirect()->back()->with('success', 'User updated successfully');
 
 
     }
     public function updateIdstatus($id)
     {
         $affected = IdcardDetail::where('id', $id)
                         ->update(['status' => 'deleted']);
 
             return redirect()->back()->with('success', 'Data delete successfully.');
     
     }
     public function updateAddressInfo(Request $request,$id){
        
         // Retrieve the user record
     // dd($id);
         $userdata = Addressdetail::find($id);
         // $userdata = new Addressdetail();
         $userdata->userid = $request->userid;
         $userdata->preaddress = $request->preaddress;
         $userdata->prepost = $request->prepost;
         $userdata->predistrict = $request->predistrict;
         $userdata->prestate = $request->prestate;
         $userdata->precountry = $request->precountry;
         $userdata->prepincode = $request->prepincode;
         $userdata->prelandmark = $request->prelandmark;
 
         $userdata->peraddress = $request->peraddress;
         $userdata->perpost = $request->perpost;
         $userdata->perdistri = $request->perdistri;
         $userdata->perstate = $request->perstate;
         $userdata->percountry = $request->percountry;
         $userdata->perpincode = $request->perpincode;
         $userdata->perlandmark = $request->perlandmark;
         $userdata->update();
  
  
         return redirect()->back()->with('success', 'Address updated successfully');
      }
 
      public function updatenewAddress(Request $request){
        
         // Retrieve the user record
     // dd($id);
         // $userdata = Addressdetail::find($id);
         $userdata = new Addressdetail();
         $userdata->userid = $request->userid;
         $userdata->preaddress = $request->preaddress;
         $userdata->prepost = $request->prepost;
         $userdata->predistrict = $request->predistrict;
         $userdata->prestate = $request->prestate;
         $userdata->precountry = $request->precountry;
         $userdata->prepincode = $request->prepincode;
         $userdata->prelandmark = $request->prelandmark;
 
         $userdata->peraddress = $request->peraddress;
         $userdata->perpost = $request->perpost;
         $userdata->perdistri = $request->perdistri;
         $userdata->perstate = $request->perstate;
         $userdata->percountry = $request->percountry;
         $userdata->perpincode = $request->perpincode;
         $userdata->perlandmark = $request->perlandmark;
         $userdata->save();
  
  
         return redirect()->back()->with('success', 'Address updated successfully');
      }
      public function updateBankInfo(Request $request,$id){
        
         // Retrieve the user record
     // dd($id);
         $bankdata = Bankdetail::find($id);
         $bankdata->userid = $request->userid;
         $bankdata->bankname = $request->bankname;
         $bankdata->branchname = $request->branchname;
         $bankdata->ifsccode = $request->ifsccode;
         $bankdata->accname = $request->accname;
         $bankdata->accnumber = $request->accnumber;
         $bankdata->update();
  
  
         return redirect()->back()->with('success', 'Bank Details updated successfully');
      }
 
      public function updatenewBankInfo(Request $request){
        
         // Retrieve the user record
     // dd($id);
         // $bankdata = Bankdetail::find($id);
         $bankdata->userid = $request->userid;
         $bankdata->bankname = $request->bankname;
         $bankdata->branchname = $request->branchname;
         $bankdata->ifsccode = $request->ifsccode;
         $bankdata->accname = $request->accname;
         $bankdata->accnumber = $request->accnumber;
         $bankdata->save();
  
  
         return redirect()->back()->with('success', 'Bank Details updated successfully');
      }
 
      public function updateotherInfo(Request $request,$id){
        
         // Retrieve the user record
     // dd($id);
         $userdata = User::find($id);
         // dd($userdata);
         $userdata->userid = $request->userid;
         $userdata->datejoin = $request->datejoin;
         $userdata->seba = $request->seba;
         $userdata->templeid  = $request->templeid ;
         $userdata->bedhaseba = $request->bedhaseba;
         $userdata->update();
  
  
         return redirect()->back()->with('success', 'Other Details updated successfully');
      }

      public function sebayatprofile(){
        $userid = Auth::guard('users')->user()->userid;
    
        $userinfo = User::where('userid', $userid)->first();
        $bankinfo = Bankdetail::where('userid', $userid)->first();
        $childinfos = Childrendetail::where('userid', $userid)->get();
        $iddetails = IdcardDetail::where('userid', $userid)->get();
        $address = Addressdetail::where('userid', $userid)->first();
        $bankinfo = Bankdetail::where('userid', $userid)->first();

       
        return view('user/sebayatprofile',compact('userinfo','bankinfo','childinfos','iddetails','address','bankinfo'));

    }
    public function downloadUserImage(Request $request)
    {
       

        $user = Auth::guard('users')->user()->userid;
        $iddetails = IdcardDetail::where('userid', $user)->get();
        // Fetch the authenticated user
        $imagePath = asset($iddetails->uploadoc); // Path to user's image

        // Generate PDF with the user's image
        $pdf = PDF::loadView('pdf.user_image', ['imagePath' => $imagePath]);

        // Return PDF as a downloadable response
        return $pdf->download('user_image.pdf');
    }
 
}
