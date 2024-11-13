<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\RequestException;

class FlowerRegistrationController extends Controller
{
    //
    private $apiUrl = 'https://auth.otpless.app';
    private $clientId = 'Q9Z0F0NXFT3KG3IHUMA4U4LADMILH1CB';
    private $clientSecret = '5rjidx7nav2mkrz9jo7f56bmj8zuc1r2';
    
    public function flowerregistration(Request $request)
    {
        return view('flower-registration');
    }
    public function sendOtpflower(Request $request)
    {
        $phoneNumber = $request->input('phone');
        $countryCode = '+91'; // Assuming the country code is +91 as in your Blade template
        $fullPhoneNumber = $countryCode . $phoneNumber;
         
        $client = new Client();
        $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/send';
    
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'clientId'      => $this->clientId,
                    'clientSecret'  => $this->clientSecret,
                ],
                'json' => [
                    'phoneNumber' => $fullPhoneNumber,
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
    
            if (isset($body['orderId'])) {
                $orderId = $body['orderId'];
                session(['otp_order_id' => $orderId, 'otp_phone' => $fullPhoneNumber]);
                return redirect()->back()->with('otp_sent', true)->with('message', 'OTP sent successfully');
            } else {
                return redirect()->back()->with('message', 'Failed to send OTP. Please try again.');
            }
        } catch (RequestException $e) {
            return redirect()->back()->with('message', 'Failed to send OTP due to an error.');
        }
    }

    public function verifyOtpflower(Request $request)
    {
        $orderId = session('otp_order_id');
        $otp = $request->input('otp');
        $phoneNumber = session('otp_phone');
        
        // OTP verification logic
        $client = new Client();
        $url = rtrim($this->apiUrl, '/') . '/auth/otp/v1/verify';
    
        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'clientId' => $this->clientId,
                    'clientSecret' => $this->clientSecret,
                ],
                'json' => [
                    'orderId' => $orderId,
                    'otp' => $otp,
                    'phoneNumber' => $phoneNumber,
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
    
            if (isset($body['isOTPVerified']) && $body['isOTPVerified']) {
                // Check if mobile number exists in the temple__user_login table
                // $phoneNumber = str_replace('+91', '', $phoneNumber);
                $temple = User::where('mobile_number', $phoneNumber)->first();
    
                if ($temple) {
                    // Mobile number exists, log the user in and redirect to dashboard
                    Auth::guard('users')->login($temple);
                    return redirect()->route('floweruseraddress')->with('success', 'User authenticated successfully.');
                } else {
                    // Mobile number does not exist, redirect to registration page
                    return redirect()->route('flowerregistration')->with('message', 'Please complete your registration.');
                }
            } else {
                $message = $body['message'] ?? 'Invalid OTP';
                return redirect()->back()->with('message', $message);
            }
        } catch (RequestException $e) {
            return redirect()->back()->with('message', 'Failed to verify OTP due to an error.');
        }
    }
    
    public function floweruseraddress(Request $request)
    {
       
        return view('flower-user-address');
    }
    public function flowersaveaddress(Request $request)
    {
        $user = Auth::guard('users')->user();
        $userid = $user->userid;
    
        // Check if the user already has active addresses
        $hasAddresses = UserAddress::where('user_id', $userid)
                                    ->where('status', 'active')->exists();
    
        // Create the new address
        $addressdata = new UserAddress();
        $addressdata->user_id = $userid;
        $addressdata->country = 'India';
        $addressdata->state = $request->state;
        $addressdata->city = $request->city;
        $addressdata->pincode = $request->pincode;
        $addressdata->area = "null";
        $addressdata->address_type = "null";
        $addressdata->apartment_flat_plot = $request->apartment_flat_plot; // Add apartment/flat/plot field
        $addressdata->landmark = $request->landmark; // Add landmark field
        $addressdata->locality = $request->locality; // Add locality field
        $addressdata->place_category = $request->place_category; // Add locality field

        $addressdata->status = 'active';
    
        // Set as default if it's the first address
        if (!$hasAddresses) {
            $addressdata->default = 1;
        }
    
        // Save the address to the database
        $addressdata->save();
    
        // Return a view with SweetAlert script
        return view('flower-user-address')->with([
            'successMessage' => 'You have successfully registered for free flower!',
            'playStoreLink' => 'https://play.google.com/store/apps/details?id=com.yourapp' // Replace with your actual Play Store link
        ]);
    }
    
}
