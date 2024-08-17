<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'nip' => 'required|integer|digits_between:1,255|unique:users',
            'nisn' => 'required|integer|digits_between:1,255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'nisn' => $request->nisn,
            'password' => bcrypt($request->password),
            'id_role' => $request->id_role,
        ]);

        // return response
        if($user) {
            return response()->json([
                'success' => true,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);


    }
}
