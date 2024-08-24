<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Profile;
use App\Models\Rating;
use App\Models\Poojastatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoojaHistoryController extends Controller
{
    public function poojahistory()
    {
        $pandit = Auth::guard('pandits')->user();
    
        // Fetch the pandit's profile details using their pandit_id
        $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();
        
        // Fetch completed Poojas
        $complete_pooja = Booking::with(['poojaList', 'poojaStatus'])
            ->where('pooja_status', 'completed')
            ->where('pandit_id', $pandit_details->id)
            ->get();
    
        // Fetch ratings for each completed pooja
        foreach ($complete_pooja as $pooja) {
            $pooja->rating = Rating::where('booking_id', $pooja->booking_id)->first();
        }
    
        // Fetch all Poojas for the Pandit
        $all_poojas = Booking::with(['poojaList', 'poojaStatus'])
            ->join('pooja_list', 'bookings.pooja_id', '=', 'pooja_list.id')
            ->where('bookings.pandit_id', $pandit_details->id)
            ->where('bookings.payment_status', 'paid')
            ->where('bookings.application_status', 'approved')
            ->where('bookings.pooja_status', '!=', 'canceled')
            ->orderBy('bookings.booking_date', 'asc')
            ->select('bookings.*', 'pooja_list.pooja_name as pooja_name')
            ->get();
    
        // Filter to include only Poojas that have started but not ended
        $all_poojas = $all_poojas->filter(function ($booking) {
            $status = Poojastatus::where('booking_id', $booking->booking_id)
                ->where('pooja_id', $booking->pooja_id)
                ->first();
            
            $booking->status = $status;
    
            return $status && $status->start_time && !$status->end_time;
        });
    
        return view('pandit.poojahistory', compact('complete_pooja', 'all_poojas'));
    }
    
    
}
