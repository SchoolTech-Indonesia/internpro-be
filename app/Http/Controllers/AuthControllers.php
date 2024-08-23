<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class AuthControllers extends Controller
{
    public function generateOtp(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'Request body cannot be empty'], 400);
        }

        $validator = Validator::make($request->all(), [
            'nip_nisn' => 'required|string|filled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('nip', $request->nip_nisn)
            ->orWhere('nisn', $request->nip_nisn)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if user has email
        if (!$user->email) {
            return response()->json(['message' => 'Reset Password harus menghubungi Admin!'], 404);
        }

        // Generate a safest random OTP
        $otp = random_int(100000, 999999);

        $user->update([
            'otp' => $otp,
            'otp_expired_at' => Carbon::now()->addMinutes(5),
        ]);

        try {
            Mail::to($user->email)->send(new OtpEmail($otp, $user->name));

            return response()->json(['message' => 'OTP sent to your email'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email. Please try again later.'], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'Request body cannot be empty'], 400);
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6', // digit limitation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $key = 'otp-attempt:' . $request->ip();

        // attempt limitation
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.'
            ], 429);
        }

        $user = User::where('otp', $request->otp)->first();

        if (!$user) {
            RateLimiter::hit($key, 60); // cooldown
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->gt($user->otp_expired_at)) {
            // expired otp handler
            $user->update([
                'otp' => null,
                'otp_expired_at' => null,
            ]);
            return response()->json(['message' => 'Expired OTP'], 400);
        } else {
            // reset the otp if verified
            $user->update([
                'otp' => null,
                'otp_expired_at' => null,
            ]);

            RateLimiter::clear($key); // reset the attempt if verified
            return response()->json([
                'message' => 'OTP verified',
                'user' => $user->only('id', 'email'),
            ], 200);
        } 
    }

    public function resetPassword(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'Request body cannot be empty'], 400);
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|filled',
            'password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid OTP'
            ], 400);
        }

        if ($user->otp_expired_at < Carbon::now()) {
            return response()->json([
                'message' => 'OTP expired'
            ], 400);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'otp' => null,
            'otp_expired_at' => null,
        ]);

        return response()->json(['message' => 'Password reset successful'], 200);
    }
}
