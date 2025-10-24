<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerVendor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class VendorOtpController extends Controller
{
    /**
     * Send OTP only if vendor exists.
     * If vendor doesn't exist => tell user to contact admin (no OTP is sent).
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        // 1) Look up vendor by phone
        $vendor = FlowerVendor::where('phone_no', $phone)->first();

        if (!$vendor) {
            // ❌ No auto-create, no OTP
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered. Please contact admin.',
            ], 404);
        }

        // 2) Generate OTP + short token
        $otp        = random_int(100000, 999999);
        $shortToken = Str::upper(Str::random(6));

        // 3) Persist OTP with optional expiry/attempts if columns exist
        $vendor->otp = (string) $otp;

        if ($this->columnExists($vendor, 'otp_expires_at')) {
            $vendor->otp_expires_at = Carbon::now()->addMinutes(10);
        }
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $vendor->otp_attempts = 0;
        }
        $vendor->save();

        // 4) Local/testing shortcut (still requires vendor to exist)
        $isTest = app()->environment(['local','testing']) && $phone === '+919876543210';
        if ($isTest) {
            return response()->json([
                'success'   => true,
                'message'   => 'Static OTP generated (test mode).',
                'otp'       => $otp, // only in non-prod
                'token'     => $shortToken,
                'vendor_id' => $vendor->vendor_id,
            ], 200);
        }

        // 5) Send via MSG91 WhatsApp template
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type"      => "template",
            "payload"           => [
                "messaging_product" => "whatsapp",
                "to"                => $phone,
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
     * Verify vendor OTP (vendor must already exist).
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required','string'],
            'otp'   => ['required','digits_between:4,8'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        $vendor = FlowerVendor::where('phone_no', $phone)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered. Please contact admin.',
            ], 404);
        }

        // Expiry check
        if ($this->columnExists($vendor, 'otp_expires_at') && $vendor->otp_expires_at instanceof Carbon) {
            if (Carbon::now()->greaterThan($vendor->otp_expires_at)) {
                $vendor->otp = null;
                $vendor->save();
                return response()->json([
                    'success' => false,
                    'message' => 'OTP expired. Please request a new one.',
                ], 410);
            }
        }

        // Attempts check
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $maxAttempts = 5;
            if ((int) $vendor->otp_attempts >= $maxAttempts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many invalid attempts. Please request a new OTP.',
                ], 429);
            }
        }

        // Compare OTP
        if ((string) $vendor->otp !== (string) $validated['otp']) {
            if ($this->columnExists($vendor, 'otp_attempts')) {
                $vendor->otp_attempts = (int) $vendor->otp_attempts + 1;
                $vendor->save();
            }
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 401);
        }

        // Success → clear OTP + counters
        $vendor->otp = null;
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $vendor->otp_attempts = 0;
        }
        if ($this->columnExists($vendor, 'otp_expires_at')) {
            $vendor->otp_expires_at = null;
        }
        $vendor->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Vendor verified successfully.',
            'vendor_id' => $vendor->vendor_id,
            'vendor'    => $vendor,
        ], 200);
    }

    /** Helpers */
    private function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw ?? '');
        if (\Illuminate\Support\Str::startsWith($digits, '91') && strlen($digits) >= 12) {
            return '+' . $digits;
        }
        if (strlen($digits) === 10) {
            return '+91' . $digits;
        }
        return \Illuminate\Support\Str::startsWith($raw, '+') ? $raw : '+' . $digits;
    }

    private function columnExists($model, string $column): bool
    {
        return array_key_exists($column, $model->getAttributes()) ||
               in_array($column, $model->getFillable(), true) ||
               $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
