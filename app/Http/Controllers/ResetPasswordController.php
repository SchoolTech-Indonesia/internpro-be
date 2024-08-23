<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    private function validateOtp($user): JsonResponse
    {
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
        return response()->json();
    }

    public function update(ResetPasswordRequest $request) : JsonResponse
    {
        $user = User::where('otp', $request->otp)->first();

        $this->validateOtp($user);

        $user->update([
            'password' => ($request->password),
            'otp' => null,
            'otp_expired_at' => null,
        ]);

        return response()->json(['message' => 'Password reset successful']);
    }
}
