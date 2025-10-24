<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlowerVendor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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

        // Build ALL candidate phone formats so we can match DB values reliably
        $candidates = $this->phoneCandidates($validated['phone']);

        // Look up vendor by any variant (your DB often stores 10-digit values)
        $vendor = FlowerVendor::whereIn('phone_no', $candidates)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered. Please contact admin.',
            ], 404);
        }

        // Generate OTP + short token
        $otp        = random_int(100000, 999999);
        $shortToken = Str::upper(Str::random(6));

        // Persist OTP with optional expiry/attempts if columns exist
        $vendor->otp = (string) $otp;

        if ($this->columnExists($vendor, 'otp_expires_at')) {
            $vendor->otp_expires_at = Carbon::now()->addMinutes(10);
        }
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $vendor->otp_attempts = 0;
        }
        $vendor->save();

        // Local/testing shortcut (still requires vendor to exist)
        $isTest = app()->environment(['local','testing']) && in_array('+919876543210', $candidates, true);
        if ($isTest) {
            return response()->json([
                'success'   => true,
                'message'   => 'Static OTP generated (test mode).',
                'otp'       => $otp, // only in non-prod
                'token'     => $shortToken,
                'vendor_id' => $vendor->vendor_id,
            ], 200);
        }

        // Send via MSG91 WhatsApp (template must match your MSG91 config)
        $payload = [
            "integrated_number" => env('MSG91_WA_NUMBER'),
            "content_type"      => "template",
            "payload"           => [
                "messaging_product" => "whatsapp",
                "to"                => $candidates[0], // prefer +E164 if available
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
     * Verify vendor OTP (time-bound; returns Sanctum Bearer token on success).
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required','string'],
            'otp'   => ['required','digits_between:4,8'],
        ]);

        // Try all likely DB-stored formats for phone_no
        $candidates = $this->phoneCandidates($validated['phone']);

        $vendor = FlowerVendor::whereIn('phone_no', $candidates)->first();

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not registered. Please contact admin.',
            ], 404);
        }

        // Expiry check (if column exists & is set)
        if ($this->columnExists($vendor, 'otp_expires_at') && $vendor->otp_expires_at instanceof Carbon) {
            if (Carbon::now()->greaterThan($vendor->otp_expires_at)) {
                // Clear expired OTP to prevent reuse
                $vendor->otp = null;
                $vendor->save();

                return response()->json([
                    'success' => false,
                    'message' => 'OTP expired. Please request a new one.',
                ], 410);
            }
        }

        // Attempts check (if column exists)
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

        // ✅ OTP is valid — clear OTP & counters first (prevents reuse even if status blocks login)
        $vendor->otp = null;
        if ($this->columnExists($vendor, 'otp_attempts')) {
            $vendor->otp_attempts = 0;
        }
        if ($this->columnExists($vendor, 'otp_expires_at')) {
            $vendor->otp_expires_at = null;
        }
        $vendor->save();

        // Block inactive vendors from receiving tokens
        if (isset($vendor->status) && $vendor->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Vendor is not active. Please contact admin.',
            ], 403);
        }

        // Create Sanctum token (time-limited via config/sanctum.php 'expiration')
        $accessToken    = $vendor->createToken('vendor-api', ['vendor']);
        $plainTextToken = $accessToken->plainTextToken;

        $ttlMinutes = (int) (config('sanctum.expiration') ?? 0);
        $expiresAt  = $ttlMinutes > 0 ? now()->addMinutes($ttlMinutes) : null;

        return response()->json([
            'success'      => true,
            'message'      => 'Vendor verified successfully.',
            'vendor_id'    => $vendor->vendor_id,
            'token_type'   => 'Bearer',
            'access_token' => $plainTextToken,
            'expires_at'   => optional($expiresAt)->toIso8601String(), // null if no expiry configured
            'vendor'       => $vendor,
        ], 200);
    }

    /** ---------- Helpers ---------- */

    /**
     * Build all likely representations of the phone found in your DB.
     * Examples:
     *  - "7749968976"     -> ["7749968976", "+917749968976", "917749968976"]
     *  - "+917749968976"  -> ["+917749968976", "917749968976", "7749968976", "+917749968976"]
     */
    private function phoneCandidates(string $raw): array
    {
        $raw    = trim($raw);
        $digits = preg_replace('/\D+/', '', $raw);

        $set = [];

        if ($digits === '') {
            return [$raw];
        }

        // If 10-digit Indian mobile, generate 10, +91 + 10, and 91 + 10
        if (strlen($digits) === 10) {
            $set[] = $digits;
            $set[] = '+91' . $digits;
            $set[] = '91' . $digits;
        }
        // If it's 91 + 10 digits
        elseif (Str::startsWith($digits, '91') && strlen($digits) === 12) {
            $ten = substr($digits, -10);
            $set[] = $digits;          // "91XXXXXXXXXX"
            $set[] = '+' . $digits;    // "+91XXXXXXXXXX"
            $set[] = $ten;             // "XXXXXXXXXX"
            $set[] = '+91' . $ten;     // "+91XXXXXXXXXX"
        } else {
            // Fallback: try raw, digits, +digits
            $set[] = $raw;
            $set[] = $digits;
            $set[] = '+' . $digits;
        }

        // Also include the raw input (in case DB stores spaces/punctuation)
        $set[] = $raw;

        // Unique + non-empty
        return array_values(array_unique(array_filter($set, fn($v) => $v !== null && $v !== '')));
    }

    /**
     * Check if an attribute/column exists on the model/table.
     */
    private function columnExists($model, string $column): bool
    {
        return array_key_exists($column, $model->getAttributes()) ||
               in_array($column, $model->getFillable(), true) ||
               $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), $column);
    }
}
