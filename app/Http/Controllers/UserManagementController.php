<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\FlowerPayment;
use App\Models\FlowerProduct;
use App\Models\Locality;
use Illuminate\Support\Str;
use App\Models\UserDevice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Apartment;

use Illuminate\Support\Facades\Log; // Make sure to import the Log facade

class UserManagementController extends Controller
{

    public function existingUser()
    {
        $user_details = User::get();

        $flowers = FlowerProduct::where('status', 'active')
            ->where('category', 'Subscription')
            ->get();
    
        return view('existing-user-details', compact('flowers','user_details'));
    }
    
    public function getUserAddresses($userId)
    {
        $addresses = UserAddress::where('user_id', $userId)
            ->where('status', 'active')
            ->get(['id', 'address_type', 'apartment_flat_plot', 'locality', 'landmark', 'city', 'state', 'country', 'pincode', 'default']);
    
        foreach ($addresses as $address) {
            $address->locality_name = $address->localityDetails->locality_name ?? 'N/A';
        }
    
        return response()->json(['addresses' => $addresses]);
    }

    public function handleUserData(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request data
            $validated = $request->validate([
                'userid' => 'required|string',
                'address_id' => 'required|numeric',
                'start_date' => 'nullable|date',
                'duration' => 'nullable|integer|in:1,3,6',
                'payment_method' => 'nullable|string',
                'payment_status' => 'nullable|string',
                'paid_amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|string',
            ]);

            $existingSubscription = Subscription::where('user_id', $validated['userid'])->first();

            if ($existingSubscription) {
                $order = Order::where('order_id', $existingSubscription->order_id)->first();
                if ($order) {
                    $order->update([
                        'user_id' => $validated['userid'],
                        'quantity' => 1,
                        'total_price' => $validated['paid_amount'] ?? 0,
                        'address_id' => $validated['address_id'],
                    ]);
                    Log::info('Order updated successfully', ['order_id' => $order->order_id]);
                } else {
                    return back()->with('error', 'Order ID not found for update.');
                }
            } else {
                $orderId = 'ORD-' . strtoupper(Str::random(12));
                $order = Order::create([
                    'user_id' => $validated['userid'],
                    'order_id' => $orderId,
                    'quantity' => 1,
                    'start_date' => $validated['start_date'] ?? now(),
                    'address_id' => $validated['address_id'],
                    'total_price' => $validated['paid_amount'] ?? 0,
                ]);
            }

            // Calculate end date
            $startDate = $validated['start_date'] ? Carbon::parse($validated['start_date']) : now();
            $endDate = match ((int) $validated['duration']) {
                1 => $startDate->copy()->addDays(29),
                3 => $startDate->copy()->addDays(89),
                6 => $startDate->copy()->addDays(179),
                default => now(), // Provide a fallback value instead of throwing an exception
            };
            
            // Create subscription
            Subscription::create([
                'subscription_id' => 'SUB-' . strtoupper(Str::random(12)),
                'user_id' => $validated['userid'],
                'product_id' => $order->product_id ?? null,
                'order_id' => $order->order_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
                'status' => $validated['status'] ?? 'active',
            ]);

        
            // Create payment record
            FlowerPayment::create([
                'order_id' => $order->order_id,
                'payment_id' => null,
                'user_id' => $validated['userid'],
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_status' => $validated['payment_status'] ?? 'pending',
            ]);

            DB::commit();
            return back()->with('success', 'User data processed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing user data', ['error' => $e->getMessage()]);
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

   public function index(Request $request)
{
    $search     = trim($request->input('search', '')); // device-only search
    $platform   = $request->input('platform', '');
    $daterange  = trim($request->input('date_range', ''));
    $userId     = trim($request->input('user_id', '')); // explicit user filter (userid)

    // ------- Date range parsing -------
    $dateStart = null;
    $dateEnd   = null;
    if ($daterange !== '') {
        // accept "YYYY-MM-DD to YYYY-MM-DD" or "YYYY-MM-DD - YYYY-MM-DD"
        $sep = str_contains($daterange, ' to ') ? ' to ' : (str_contains($daterange, ' - ') ? ' - ' : 'to');
        if (str_contains($daterange, $sep)) {
            [$s, $e] = array_map('trim', explode($sep, $daterange));
            try {
                $dateStart = \Carbon\Carbon::parse($s)->startOfDay();
                $dateEnd   = \Carbon\Carbon::parse($e)->endOfDay();
            } catch (\Throwable $th) {
                $dateStart = $dateEnd = null;
            }
        }
    }

    // Distinct platforms for dropdown
    $platforms = \App\Models\UserDevice::query()
        ->select('platform')
        ->whereNotNull('platform')
        ->distinct()
        ->orderBy('platform')
        ->pluck('platform');

    // Normalize/validate user filter
    $selectedUser = null;
    if ($userId !== '' && strtoupper($userId) !== 'NEW') {
        $selectedUser = \App\Models\User::where('userid', $userId)
            ->select('userid', 'name', 'mobile_number')
            ->first();
        if (!$selectedUser) {
            // If user id invalid, ignore the filter instead of forcing empty results
            $userId = '';
        }
    } else {
        // Ignore "NEW" or empty
        $userId = '';
    }

    // ------- Base query -------
    $devicesQ = \App\Models\UserDevice::query()
        ->with(['user' => function ($q) {
            $q->select('userid', 'name', 'mobile_number');
        }]);

    // Apply user filter only when valid
    if ($userId !== '') {
        $devicesQ->where('user_id', $userId);
    }

    // Device-only search
    if ($search !== '') {
        $devicesQ->where(function ($q) use ($search) {
            $q->where('device_id', 'like', "%{$search}%")
              ->orWhere('device_model', 'like', "%{$search}%")
              ->orWhere('version', 'like', "%{$search}%")
              ->orWhere('platform', 'like', "%{$search}%");
        });
    }

    if ($platform !== '') {
        $devicesQ->where('platform', $platform);
    }

    if ($dateStart && $dateEnd) {
        $devicesQ->whereBetween('last_login_time', [$dateStart, $dateEnd]);
    }

    $devices = $devicesQ
        ->orderByDesc('last_login_time')
        ->paginate(25)
        ->appends($request->query());

    // ------- Metrics (unchanged) -------
    $todayStart = \Carbon\Carbon::today()->startOfDay();
    $todayEnd   = \Carbon\Carbon::today()->endOfDay();
    $weekStart  = \Carbon\Carbon::now()->startOfWeek(); // Monday

    $todayLogins = \App\Models\UserDevice::query()
        ->whereBetween('last_login_time', [$todayStart, $todayEnd])
        ->distinct('user_id')
        ->count('user_id');

    $uniqueDevices = \App\Models\UserDevice::query()
        ->whereNotNull('device_id')
        ->distinct('device_id')
        ->count('device_id');

    $activeThisWeek = \App\Models\UserDevice::query()
        ->where('last_login_time', '>=', $weekStart)
        ->distinct('user_id')
        ->count('user_id');

    $platformBreakdown = \App\Models\UserDevice::query()
        ->select('platform', \DB::raw('COUNT(DISTINCT user_id) as users'))
        ->whereNotNull('platform')
        ->groupBy('platform')
        ->orderByDesc('users')
        ->get();

    $recentLogins = \App\Models\UserDevice::query()
        ->with(['user' => function ($q) { $q->select('userid', 'name'); }])
        ->orderByDesc('last_login_time')
        ->limit(10)
        ->get();

    return view('admin.user-login-details', [
        'devices'           => $devices,
        'platforms'         => $platforms,
        'search'            => $search,
        'platform'          => $platform,
        'date_range'        => $daterange,
        'user_id'           => $userId,        // final (may be blank if invalid)
        'selectedUser'      => $selectedUser,  // used for preselect label
        'todayLogins'       => $todayLogins,
        'uniqueDevices'     => $uniqueDevices,
        'activeThisWeek'    => $activeThisWeek,
        'platformBreakdown' => $platformBreakdown,
        'recentLogins'      => $recentLogins,
    ]);
}


}
