<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserUnauthorisedDevices;

class PushNotificationController extends Controller
{
    //
    public function saveToken(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
        ]);

        UserDevice::updateOrCreate(
            ['device_id' => $request->device_id],
            ['device_model' => $request->device_model],
            ['platform' => $request->platform]
        );

        return response()->json(['message' => 'Device token saved successfully.']);
    }
}
