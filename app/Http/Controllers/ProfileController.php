<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    public function getProfile(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first();
        // Load common relationship first
        $user->load('school');
    
        // Add conditional loading based on role
        switch ($role) {
            case 'Administrator':
            case 'Teacher':
                // No additional relationships needed for these roles
                break;
            case 'Koordinator':
                $user->load('major');
                break;
            case 'Student':
                $user->load('major', 'class');
                break;
            case 'Mentor':
                $user->load('partners');
                break;
            default:
                break;
        }
    
        return response()->json([
            "success" => true,
            "data" => new ProfileResource($user),
        ], 200);
    }
    


    public function updateProfile(Request $request)
    {
        $user = auth()->guard('api')->user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(Auth::user()), // Abaikan email user saat ini untuk validasi unik
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,13}$/',
                Rule::unique('users', 'phone_number')->ignore(Auth::user()),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update user profile
        User::where('uuid', $user->uuid)->update([
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully!',
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = auth()->guard("api")->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                "success" => false,
                "message" => "Password yang Anda masukkan salah, Silahkan coba lagi."
            ], 400);
        }

        User::where('uuid', $user->uuid)->update([
            'password' => bcrypt($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ], 200);
    }

}