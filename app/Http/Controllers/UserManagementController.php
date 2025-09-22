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
        $search    = trim($request->input('search', ''));
        $platform  = $request->input('platform', '');
        $daterange = $request->input('date_range', ''); // e.g. "2025-09-01 to 2025-09-22"

        // ------- Filters (date range) -------
        $dateStart = null;
        $dateEnd   = null;
        if ($daterange && str_contains($daterange, 'to')) {
            [$s, $e] = array_map('trim', explode('to', $daterange));
            try {
                $dateStart = Carbon::parse($s)->startOfDay();
                $dateEnd   = Carbon::parse($e)->endOfDay();
            } catch (\Throwable $th) {
                $dateStart = $dateEnd = null;
            }
        }

        // A distinct list of platforms for the filter dropdown
        $platforms = UserDevice::query()
            ->select('platform')
            ->whereNotNull('platform')
            ->distinct()
            ->orderBy('platform')
            ->pluck('platform');

        // ------- Base query for table -------
        $devicesQ = UserDevice::query()
            ->with(['user' => function ($q) {
                // Assume users table has columns: userid (PK), name, mobile
                $q->select('userid', 'name', 'mobile');
            }]);

        // Search across user name/mobile and device fields
        if ($search !== '') {
            $devicesQ->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%")
                  ->orWhere('version', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%");
                  });
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

        // ------- Metrics for cards -------
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();
        $weekStart  = Carbon::now()->startOfWeek(); // Monday start (adjust if needed)

        // Distinct users who logged in today
        $todayLogins = UserDevice::query()
            ->whereBetween('last_login_time', [$todayStart, $todayEnd])
            ->distinct('user_id')
            ->count('user_id');

        // Distinct devices overall
        $uniqueDevices = UserDevice::query()
            ->whereNotNull('device_id')
            ->distinct('device_id')
            ->count('device_id');

        // Distinct users active this week
        $activeThisWeek = UserDevice::query()
            ->where('last_login_time', '>=', $weekStart)
            ->distinct('user_id')
            ->count('user_id');

        // Platform breakdown (by latest activity per user to avoid overcounting)
        $platformBreakdown = UserDevice::query()
            ->select('platform', DB::raw('COUNT(DISTINCT user_id) as users'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('users')
            ->get();

        // Recent logins (for a small sidebar list)
        $recentLogins = UserDevice::query()
            ->with(['user' => function ($q) { $q->select('userid', 'name'); }])
            ->orderByDesc('last_login_time')
            ->limit(10)
            ->get();

        return view('admin.user-login-details', [
            'devices'          => $devices,
            'platforms'        => $platforms,
            'search'           => $search,
            'platform'         => $platform,
            'date_range'       => $daterange,

            'todayLogins'      => $todayLogins,
            'uniqueDevices'    => $uniqueDevices,
            'activeThisWeek'   => $activeThisWeek,
            'platformBreakdown'=> $platformBreakdown,
            'recentLogins'     => $recentLogins,
        ]);
    }

}
