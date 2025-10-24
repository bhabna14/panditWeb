<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlowerVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class VendorOtpController extends Controller
{

    public function loginPassword(Request $request)
    {
        $data = $request->validate([
            'email_id' => ['required', 'string'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        // Normalize and look up by email_id
        $email  = trim($data['email_id']);
        $vendor = FlowerVendor::where('email_id', $email)->first();

        if (!$vendor || !$vendor->password || !\Illuminate\Support\Facades\Hash::check($data['password'], $vendor->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Optional status check
        if (isset($vendor->status) && $vendor->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Account is not active.',
            ], 423);
        }

        // Create a Sanctum token (default device name)
        $token = $vendor->createToken('vendor-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'vendor'  => $vendor,
            'token'   => $token,
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone'     => ['nullable','string'],
            'vendor_id' => ['nullable','string'],
        ]);

        if (empty($validated['phone']) && empty($validated['vendor_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provide either phone or vendor_id.',
            ], 422);
        }

        // Resolve vendor + phone
        $vendor = null;
        $candidates = [];

        if (!empty($validated['vendor_id'])) {
            $vendor = FlowerVendor::find($validated['vendor_id']);
            if (!$vendor) {
                return response()->json(['success' => false,'message' => 'Vendor not found.'], 404);
            }
            if (!$vendor->phone_no) {
                return response()->json(['success' => false,'message' => 'Vendor does not have a phone number on file.'], 422);
            }
            $candidates = $this->phoneCandidates($vendor->phone_no);
        } else {
            $candidates = $this->phoneCandidates($validated['phone']);
            $vendor = FlowerVendor::whereIn('phone_no', $candidates)->first();
            if (!$vendor) {
                return response()->json(['success' => false,'message' => 'Mobile number not registered. Please contact admin.'], 404);
            }
        }

        // Generate OTP + short token
        $otp        = random_int(100000, 999999);
        $shortToken = Str::upper(Str::random(6));

        // Persist OTP
        $vendor->otp = (string) $otp;

        if ($this->columnExists($vendor, 'otp_expires_at')) {
            $vendor->otp_expires_at = Carbon::now()->addMinutes(10);
        }
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $vendor->otp_attempts = 0;
        }
        $vendor->save();

        // Local/testing shortcut
        $isTest = app()->environment(['local','testing']) && in_array('+919876543210', $candidates, true);
        if ($isTest) {
            return response()->json([
                'success'   => true,
                'message'   => 'Static OTP generated (test mode).',
                'otp'       => $otp, // only for non-prod
                'token'     => $shortToken,
                'vendor_id' => $vendor->vendor_id,
            ], 200);
        }

        // MSG91 WhatsApp payload
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type"      => "template",
            "payload"           => [
                "messaging_product" => "whatsapp",
                "to"                => $candidates[0], // E.164 preferred
                "type"              => "template",
                "template"          => [
                    "name"       => env('MSG91_WA_TEMPLATE_VENDOR', env('MSG91_WA_TEMPLATE')),
                    "language"   => ["code" => "en", "policy" => "deterministic"],
                    "namespace"  => env('MSG91_WA_NAMESPACE'),
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => (string) $otp],
                            ],
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => 0,
                            "parameters" => [
                                ["type" => "text", "text" => $shortToken],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'authkey'      => env('MSG91_AUTHKEY'),
            ])->timeout(10)->post(
                'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/',
                $payload
            );

            $result = $response->json();

            if ($response->status() === 401 || ($result['status'] ?? '') === 'fail') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized or template failure. Check MSG91 credentials/template.',
                    'error'   => $result,
                ], 401);
            }

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'MSG91 request failed.',
                    'error'   => $result ?? $response->body(),
                ], 502);
            }

            return response()->json([
                'success'    => true,
                'message'    => 'OTP sent successfully to vendor.',
                'vendor_id'  => $vendor->vendor_id,
                'token'      => $shortToken,
                'api_result' => $result,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP via WhatsApp.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify WhatsApp OTP and mint token
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone'       => ['nullable','string'],
            'vendor_id'   => ['nullable','string'],
            'otp'         => ['required','digits_between:4,8'],
            'device_name' => ['sometimes','string','max:100'],
        ]);

        if (empty($validated['phone']) && empty($validated['vendor_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provide either phone or vendor_id.',
            ], 422);
        }

        // Resolve vendor
        $vendor = !empty($validated['vendor_id'])
            ? FlowerVendor::find($validated['vendor_id'])
            : FlowerVendor::whereIn('phone_no', $this->phoneCandidates($validated['phone']))->first();

        if (!$vendor) {
            return response()->json(['success' => false,'message' => 'Vendor not found.'], 404);
        }

        // Expiry check
        if ($this->columnExists($vendor, 'otp_expires_at') && $vendor->otp_expires_at instanceof Carbon) {
            if (Carbon::now()->greaterThan($vendor->otp_expires_at)) {
                $vendor->otp = null;
                $vendor->save();
                return response()->json(['success' => false,'message' => 'OTP expired. Please request a new one.'], 410);
            }
        }

        // Attempts limit
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $maxAttempts = 5;
            if ((int) $vendor->otp_attempts >= $maxAttempts) {
                return response()->json(['success' => false,'message' => 'Too many invalid attempts. Please request a new OTP.'], 429);
            }
        }

        // Compare OTP
        if ((string) $vendor->otp !== (string) $validated['otp']) {
            if ($this->columnExists($vendor, 'otp_attempts')) {
                $vendor->otp_attempts = (int) $vendor->otp_attempts + 1;
                $vendor->save();
            }
            return response()->json(['success' => false,'message' => 'Invalid OTP.'], 401);
        }

        // Success → clear OTP + counters
        $vendor->otp = null;
        if ($this->columnExists($vendor, 'otp_attempts')) $vendor->otp_attempts = 0;
        if ($this->columnExists($vendor, 'otp_expires_at')) $vendor->otp_expires_at = null;
        $vendor->save();

        if (isset($vendor->status) && $vendor->status !== 'active') {
            return response()->json(['success' => false,'message' => 'Account is not active.'], 423);
        }

        $device = $validated['device_name'] ?? 'vendor-api';
        $token  = $vendor->createToken($device)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Vendor verified successfully.',
            'vendor'  => $vendor,
            'token'   => $token,
        ], 200);
    }

    /* ----------------- Helpers ----------------- */

    private function phoneCandidates(string $raw): array
    {
        $raw    = trim($raw);
        $digits = preg_replace('/\D+/', '', $raw);
        $set = [];

        if ($digits === '') return [$raw];

        if (strlen($digits) === 10) {
            $set[] = $digits;           // 98XXXXXXXX
            $set[] = '+91'.$digits;     // +9198XXXXXXXX
            $set[] = '91'.$digits;      // 9198XXXXXXXX
        } elseif (str_starts_with($digits, '91') && strlen($digits) === 12) {
            $ten   = substr($digits, -10);
            $set[] = $digits;
            $set[] = '+'.$digits;
            $set[] = $ten;
            $set[] = '+91'.$ten;
        } else {
            $set[] = $raw;
            $set[] = $digits;
            $set[] = '+'.$digits;
        }

        $set[] = $raw;
        return array_values(array_unique(array_filter($set, fn($v) => $v !== '')));
    }

    private function columnExists($model, string $column): bool
    {
        return array_key_exists($column, $model->getAttributes()) ||
               in_array($column, $model->getFillable(), true) ||
               $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
