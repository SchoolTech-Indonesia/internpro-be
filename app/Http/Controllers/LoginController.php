<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
            'nip' => 'required_without:nisn',
            'nisn' => 'required_without:nip',
            'password' => 'required',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // get credentials
        $credentials = $request->only(['nip', 'nisn', 'password']);

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
            'user'    => auth()->guard('api')->user(),
            'token'   => $token
        ], 200);
    }
}
