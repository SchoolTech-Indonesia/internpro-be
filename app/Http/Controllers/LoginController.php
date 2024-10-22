<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip_nisn' => 'required|string|filled',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only(['nip_nisn', 'password']);

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP/NISN atau Password Anda salah'
            ], 401);
        }

        $user = User::with('school')->where("uuid", auth()->guard('api')->user()->uuid)->first();

        if ($user->school->end_member < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Keanggotaan Anda di sekolah ini sudah berakhir, Anda tidak bisa login.'
            ], 403);
        }

        $permissions = $user->getAllPermissions()->pluck("name");

        $customClaims = [
            'permissions' => $permissions,
        ];

        $token = JWTAuth::claims($customClaims)->fromUser($user);

        return response()->json([
            'success' => true,
            // 'user' => new UserResource(auth()->guard('api')->user()),
            'message' => 'Login success',
            'token' => $token,
        ], 200);
    }
}
