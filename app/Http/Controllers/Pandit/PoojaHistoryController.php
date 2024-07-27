<?php

namespace App\Http\Controllers\pandit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoojaHistoryController extends Controller
{
    public function poojahistory()
    {
        $user = Auth::guard('pandits')->user();

        $complete_pooja = Booking::with(['poojaList', 'poojaStatus'])
                                ->where('application_status', 'completed')
                                ->where('pandit_id', $user->id)
                                ->get();

        return view('pandit.poojahistory', compact('complete_pooja'));
    }
}
