<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            Mail::raw("Your OTP for password reset is: $otp (valid for 5 minutes)", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('SchoolTech Password Reset OTP');
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email. Please try again later.'], 500);
        }

        return response()->json(['message' => 'OTP sent to your email'], 200);
    }

    public function verifyOtp(Request $request)
    {
        if (empty($request->all())) {
            return response()->json(['message' => 'Request body cannot be empty'], 400);
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|filled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (Carbon::now()->gt($user->otp_expired_at)) {
            return response()->json(['message' => 'Expired OTP'], 400);
        }

        return response()->json(['message' => 'OTP verified'], 200);
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
