<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Models\PanditLogin;


class OrderController extends Controller
{
    //
    public function manageorders(){
        $bookings = Booking::with('pooja','pandit','payment') // Load relationship to get pooja details
                            ->orderByDesc('created_at')
                            ->get();
        return view('admin/manageorders',compact('bookings'));
    }
    public function showbooking($id)
    {
        $booking = Booking::with(['pooja', 'pandit', 'address', 'user', 'poojaStatus', 'ratings'])->findOrFail($id);
        
         $pandit_id = $booking->pandit->pandit_id;

         $pandit_login = PanditLogin::where('pandit_id', $pandit_id)->first();
         
        return view('admin/showbookingdetails', compact('booking','pandit_login'));
    }
    public function deleteBooking($id)
{
    // Find the booking by ID and delete it
    $booking = Booking::findOrFail($id);
    $booking->delete();

    // Redirect back with a success message
    return redirect()->route('manageorders')->with('success', 'Booking deleted successfully.');
}

}
