<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    private function validateOtp($user): ?JsonResponse
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
        return null;
    }


    public function store(ResetPasswordRequest $request) : JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }

        $user = User::where('otp', $request->otp)->first();

        $validation = $this->validateOtp($user);
        if($validation) {
            return $validation;
        }

        $user->update([
            'password' => ($request->password),
            'otp' => null,
            'otp_expired_at' => null,
        ]);

        return response()->json(['message' => 'Password reset successful']);
    }
}
