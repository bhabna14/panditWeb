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



class PanditController extends Controller
{
    //Pandit Profile section
    public function panditprofiles(){
        $languages = [
            'English','Odia','Hindi','Assamese', 'Bengali', 'Bodo', 'Dogri', 'Gujarati', 'Kannada', 'Kashmiri',
            'Konkani', 'Maithili', 'Malayalam', 'Manipuri', 'Marathi', 'Nepali', 'Punjabi',
            'Sanskrit', 'Santali', 'Sindhi', 'Tamil', 'Telugu', 'Urdu'
        ];
        return view("panditprofile", compact('languages'));
    }

    public function saveprofile(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:profiles,email',
            'whatsappno' => 'nullable|string|max:20',
            'bloodgroup' => 'nullable|string|max:10',
            'maritalstatus' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048', // Ensure it's an image file
        ]);

        $profile = new Profile();

        $profile->profile_id = $request->profile_id;
        $profile->title = $request->title;
        $profile->name = $request->name;
        $profile->email = $request->email;
        $profile->whatsappno = $request->whatsappno;
        $profile->bloodgroup = $request->bloodgroup;
        $profile->maritalstatus = $request->marital;
        // $profile->language = $request->language;

        $pandilang = $request->input('language');
 
            $langString = implode(',', $pandilang);
            $profile->language = $langString;

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile_photo'), $filename);
            $profile->profile_photo = $filename;
        }

        if ($profile->save()) {
            return redirect("pandit/career")->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
        }
    }

    // Pandit Career Section

    public function profilecareer(){
        return view("panditcareer");
    }

    public function savecareer(Request $request)
    {
        // Validate the request
        $request->validate([
            'qualification' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
            'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'education_type.*' => 'required|string|in:10th,+2,+3,Master Degree',
            'upload_edu.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'vedic_type.*' => 'required|string|max:255',
            'upload_vedic.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
        ]);

        $career = new Career();

        $career->career_id = $request->career_id;
        $career->qualification = $request->qualification;
        $career->total_experience = $request->experience;

// Pandit Career Photo Upload

         foreach ($request->id_type as $key => $id_type) {
            $file = $request->file('upload_id')[$key];
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->move(public_path('uploads/id_proof'), $fileName);

            // Save form data to the database
            $iddata = new IdcardDetail();
            $iddata->career_id = $request->career_id;
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
            $edudata->career_id = $request->career_id;
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
            $vedicdata->career_id = $request->career_id;
            $vedicdata->vedic_type = $vedic_type;
            $vedicdata->upload_vedic = $fileName; // Save file path in the database
            $vedicdata->save();
        }

        if ($career->save()) {
            return redirect()->back()->with('success', 'Data saved successfully.');
        } else {
            return redirect()->back()->withErrors(['danger' => 'Failed to save data.']);
        }
    }
   
    public function panditlogin(){
        return view("panditlogin");
    }

    public function poojarequest(){
        return view("/pandit/poojarequest");
    }

    public function poojahistory(){
        return view("/pandit/poojahistory");
    }

    public function poojaexperties(){
        return view("/pandit/poojaexperties");
    }

    public function poojadetails(){
        return view("/pandit/poojadetails");
    }
    
    public function poojalist(){
        return view("/pandit/poojalist");
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
