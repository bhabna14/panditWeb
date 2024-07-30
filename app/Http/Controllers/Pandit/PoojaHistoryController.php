<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoojaHistoryController extends Controller
{
    public function poojahistory()
    {
        $pandit = Auth::guard('pandits')->user();

        // Fetch the pandit's profile details using their pandit_id
        $pandit_details = Profile::where('pandit_id', $pandit->pandit_id)->first();

        $complete_pooja = Booking::with(['poojaList', 'poojaStatus'])
                                ->where('application_status', 'completed')
                                ->where('pandit_id', $pandit_details->id)
                                ->get();

        return view('pandit.poojahistory', compact('complete_pooja'));
    }
}
