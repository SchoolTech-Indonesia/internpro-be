<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'nip_nisn' => 'required|string|filled',
            'password' => 'required',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $credentials = $request->only(['nip_nisn', 'password']);

            //if auth failed
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIP/NISN atau Password Anda salah'
                ], 401);
            }

            //if auth success
            return response()->json([
                'success' => true,
                'user' => new UserResource(auth()->guard('api')->user()),
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nip_nisn' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|max:255',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Create a new user
            $user = new User();
            $user->name = $request->input('name');
            $user->nip_nisn = $request->input('nip_nisn');
            $user->password = bcrypt($request->input('password'));
            $user->phone_number = $request->input('phone_number');
            $user->id_role = '1';
            $user->school_id = '1';
            $user->major_id = '1';
            $user->class_id = '1';
            $user->partner_id = '1';
            $user->save();

            // Generate token (if using JWT)
            $token = auth()->guard('api')->login($user);

            // Return success response
            return response()->json([
                'success' => true,
                'user' => new UserResource($user),
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
