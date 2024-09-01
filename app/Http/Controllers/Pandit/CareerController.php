<?php

namespace App\Http\Controllers\Pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IdcardDetail;
use App\Models\Career;
use App\Models\EduDetail;
use App\Models\VedicDetail;
use App\Models\PanditEducation;
use App\Models\PanditVedic;
use App\Models\PanditIdCard;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class CareerController extends Controller
{
    // Pandit Career Section

    public function profilecareer(){
        return view("panditcareer");
    }
    public function managecareer()
    {
            $profileId = Auth::guard('pandits')->user()->pandit_id;
            $pandit_profile = Profile::where('pandit_id', $profileId)->latest()->first();
            $pandit_career = Career::where('pandit_id', $profileId)->latest()->first();
            $pandit_idcards = PanditIdCard::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_educations = PanditEducation::where('pandit_id', $profileId)->where('status', 'active')->get();
            $pandit_vedics = PanditVedic::where('pandit_id', $profileId)->where('status', 'active')->get();
    
            return view('pandit.managecareer', compact('pandit_profile', 'pandit_career', 'pandit_idcards', 'pandit_educations', 'pandit_vedics'));
    }

    public function savecareer(Request $request)
    {
        // Validate the request
        $request->validate([
            'qualification' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'id_type.*' => 'required|string|in:adhar,voter,pan,DL,health card',
            'upload_id.*' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            'education_type.*' => 'nullable|string',
            'upload_edu.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'vedic_type.*' => 'nullable|string',
            'upload_vedic.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ]);
    
        // Get the authenticated pandit ID
        $pandit_id = Auth::guard('pandits')->user()->pandit_id;
    
        // Check if there is an existing career record for this pandit
        $existingCareer = Career::where('pandit_id', $pandit_id)->first();
    
        if (!$existingCareer) {
            // If no existing career record, proceed with saving new career data
            $career = new Career();
            $career->pandit_id = $pandit_id;
            $career->qualification = $request->qualification;
            $career->total_experience = $request->experience;
            $career->save();
        }
    
        // Pandit Career Photo Upload
        foreach ($request->id_type as $key => $id_type) {
            // Check if an ID card of this type already exists for this pandit
            $existingIdCard = IdcardDetail::where('pandit_id', $pandit_id)
                            ->where('id_type', $id_type)
                            ->first();
    
            if (!$existingIdCard) {
                // If no existing ID card of this type, save the new one
                $file = $request->file('upload_id')[$key];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/id_proof'), $fileName);
    
                // Save form data to the database
                $iddata = new IdcardDetail();
                $iddata->pandit_id = $pandit_id;
                $iddata->id_type = $id_type;
                $iddata->upload_id = $fileName; // Save file path in the database
                $iddata->save();
            }
        }
    
        // Pandit Education Photo Upload
        if ($request->has('education_type') && $request->has('upload_edu')) {
            foreach ($request->education_type as $key => $education_type) {
                // Check if an education record of this type already exists for this pandit
                $existingEduDetail = EduDetail::where('pandit_id', $pandit_id)
                                  ->where('education_type', $education_type)
                                  ->first();
    
                if ($education_type && !$existingEduDetail) { // Ensure education_type is not empty and doesn't already exist
                    $file = $request->file('upload_edu')[$key];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/edu_details'), $fileName);
    
                    // Save form data to the database
                    $edudata = new EduDetail();
                    $edudata->pandit_id = $pandit_id;
                    $edudata->education_type = $education_type;
                    $edudata->upload_education = $fileName; // Save file path in the database
                    $edudata->save();
                }
            }
        }
    
        // Pandit Vedic Photo Upload
        if ($request->has('vedic_type') && $request->has('upload_vedic')) {
            foreach ($request->vedic_type as $key => $vedic_type) {
                // Check if a vedic record of this type already exists for this pandit
                $existingVedicDetail = VedicDetail::where('pandit_id', $pandit_id)
                                    ->where('vedic_type', $vedic_type)
                                    ->first();
    
                if ($vedic_type && !$existingVedicDetail) { // Ensure vedic_type is not empty and doesn't already exist
                    $file = $request->file('upload_vedic')[$key];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/vedic_details'), $fileName);
    
                    // Save form data to the database
                    $vedicdata = new VedicDetail();
                    $vedicdata->pandit_id = $pandit_id;
                    $vedicdata->vedic_type = $vedic_type;
                    $vedicdata->upload_vedic = $fileName; // Save file path in the database
                    $vedicdata->save();
                }
            }
        }
    
        return redirect("pandit/dashboard")->with('success', 'Data saved successfully.');
    }
    

    public function deletIdproof($id)
    {
            $affected = IdcardDetail::where('id', $id)->update(['status' => 'deleted']);
                        
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

                public function updateCareer(Request $request, $id)
                {
                    // Validate the request data
                    $request->validate([
                        'qualification' => 'required|string',
                        'experience' => 'required|numeric',
                        'id_type' => 'nullable|array',
                        'upload_id' => 'nullable|array',
                        'upload_id.*' => 'file|mimes:jpg,png,pdf|max:2048',
                        'education_type' => 'nullable|array',
                        'upload_edu' => 'nullable|array',
                        'upload_edu.*' => 'file|mimes:jpg,png,pdf|max:2048',
                    ]);
                
                    // Find the career record by its ID
                    $career = Career::findOrFail($id);
                
                    // Get the authenticated Pandit's pandit_id
                    $profileId = Auth::guard('pandits')->user()->pandit_id;
                
                    // Update the scalar fields in the Career model
                    $career->pandit_id = $profileId;
                    $career->qualification = $request->qualification;
                    $career->total_experience = $request->experience;
                
                    // Pandit ID Card Photo Upload
                    if (!empty($request->id_type) && !empty($request->upload_id)) {
                        foreach ($request->id_type as $key => $id_type) {
                            $file = $request->file('upload_id')[$key];
                            $fileName = time().'_'.$file->getClientOriginalName();
                            $filePath = $file->move(public_path('uploads/id_proof'), $fileName);
                
                            // Save form data to the database
                            $iddata = new IdcardDetail();
                            $iddata->pandit_id = $profileId;
                            $iddata->id_type = $id_type;
                            $iddata->upload_id = $fileName; // Save file path in the database
                            $iddata->save();
                        }
                    }
                
                    // Pandit Education Photo Upload
                    if (!empty($request->education_type) && !empty($request->upload_edu)) {
                        foreach ($request->education_type as $key => $education_type) {
                            $file = $request->file('upload_edu')[$key];
                            $fileName = time().'_'.$file->getClientOriginalName();
                            $filePath = $file->move(public_path('uploads/edu_details'), $fileName);
                
                            // Save form data to the database
                            $edudata = new EduDetail();
                            $edudata->pandit_id = $profileId;
                            $edudata->education_type = $education_type;
                            $edudata->upload_education = $fileName; // Save file path in the database
                            $edudata->save();
                        }
                    }
                
                    // Pandit Vedic Photo Upload
                    if (!empty($request->vedic_type) && !empty($request->upload_vedic)) {
                        foreach ($request->vedic_type as $key => $vedic_type) {
                            $file = $request->file('upload_vedic')[$key];
                            $fileName = time().'_'.$file->getClientOriginalName();
                            $filePath = $file->move(public_path('uploads/vedic_details'), $fileName);
                
                            // Save form data to the database
                            $vedicdata = new VedicDetail();
                            $vedicdata->pandit_id = $profileId;
                            $vedicdata->vedic_type = $vedic_type;
                            $vedicdata->upload_vedic = $fileName; // Save file path in the database
                            $vedicdata->save();
                        }
                    }
                
                    // Save the updated career details
                    if ($career->save()) {
                        return redirect()->back()->with('success', 'Data updated successfully.');
                    } else {
                        return redirect()->back()->with('error', 'Failed to update data.');
                    }
                }

}
