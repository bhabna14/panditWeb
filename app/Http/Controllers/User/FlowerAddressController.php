<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAddress;
use App\Models\Locality;

use Illuminate\Support\Facades\Http;

class FlowerAddressController extends Controller
{
    //
    public function mnguseraddress()
    {
        $user = Auth::guard('users')->user();
        $addressdata = UserAddress::where('user_id', $user->userid)
                                    ->where('status', 'active')
                                    ->with('localityDetails') // Load the related locality data
                                    ->get();
    
        // Set `is_default` for demonstration purposes, adjust as needed
        if ($addressdata->count()) {
            $addressdata->first()->is_default = true;
        }
    
        return view('user.flower-address.mng-user-address', compact('addressdata'));
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


    public function useraddaddress() {
        $localities = Locality::where('status', 'active')->get();
        return view('user.flower-address.add-user-address', compact('localities'));
    }
    

    public function saveuseraddress(Request $request)
    {
        $user = Auth::guard('users')->user();
        $userid = $user->userid;
    
        // Check if the user already has addresses
        $hasAddresses = UserAddress::where('user_id', $userid)
                                    ->where('status', 'active')->exists();
    
        // Create the new address
        $addressdata = new UserAddress();
        $addressdata->user_id = $userid;
        $addressdata->country = 'India';
        $addressdata->state = $request->state;
        $addressdata->city = $request->city;
        $addressdata->pincode = $request->pincode;
        $addressdata->area = $request->area;
        $addressdata->address_type = $request->address_type;
        $addressdata->locality = $request->locality;
        $addressdata->place_category = $request->place_category;
        $addressdata->apartment_flat_plot = $request->apartment_flat_plot;
        $addressdata->landmark = $request->landmark;
        $addressdata->status = 'active';
    
        // Set as default if it's the first address
        if (!$hasAddresses) {
            $addressdata->default = 1;
        }
    
        $addressdata->save();
    
        return redirect()->route('mnguseraddress')->with('success', 'Address created successfully.');
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
        $addressdata->country ='India';
        $addressdata->state =$request->state;
        $addressdata->city =$request->city;
        $addressdata->pincode =$request->pincode;
        $addressdata->area =$request->area;
        $addressdata->address_type =$request->address_type;
        $addressdata->locality =$request->locality;
        $addressdata->place_category =$request->place_category;
        $addressdata->apartment_flat_plot =$request->apartment_flat_plot;
        $addressdata->landmark =$request->landmark;
        $addressdata->status = 'active';
        // Set as default if it's the first address
        if (!$hasAddresses) {
            $addressdata->default = 1;
        }
    
        try {
            $addressdata->save();
            return response()->json(['success' => true, 'message' => 'Address saved successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error('Error saving address: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving address.']);
        }
        
    
      

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
    
    public function edituseraddress($id)
    {
        $address = UserAddress::find($id);
        $localities = Locality::all(); // Fetch all localities for the dropdown
        return view('user.flower-address.edit-user-address', compact('address', 'localities'));
    }
    
    public function updateuseraddress(Request $request)
    {
        $address = UserAddress::find($request->id);

        if ($address) {
            // $address->fullname = $request->fullname;
            // $address->number = $request->number;
            $address->country ='India';
            $address->state =$request->state;
            $address->city =$request->city;
            $address->pincode =$request->pincode;
            $address->address_type =$request->address_type;
            $address->locality =$request->locality;
            $address->place_category =$request->place_category;
            $address->apartment_flat_plot =$request->apartment_flat_plot;
            $address->landmark =$request->landmark;
            $address->status = 'active';
            $address->save();

            return redirect()->route('mnguseraddress')->with('success', 'Address updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Address not found.');
        }
    }

}
