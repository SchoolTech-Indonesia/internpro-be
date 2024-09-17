<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Validator;

class MajorityController extends Controller
{
    // GET USER UUID
    public function user_uuid()
    {
        $token = Request()->bearerToken();
        $jwt = Jwt::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        $token = $jwt->sub;
        return $token;
    }
    // GET MAJORITY
    public function index()
    {
        $majors = Major::all();
        return response()->json([
            'success' => true,
            'message' => 'List of majors',
            'data' => $majors
        ], 200);
    }

    // CREATE MAJORITY
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "major_code" => "required|unique:majors|max:255",
            "major_name" => "required|string|max:255",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }
        try {
            $data = $validator->validated();
            $user = User::where('uuid', $this->user_uuid())->first();
            $data['created_by'] = $user->name;
            Major::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Major created successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // GET MAJORITY BY ID
    public function show($id)
    {
        $major = Major::where('uuid', $id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Major retrieved successfully.',
            'data' => $major
        ], 200);
    }


    // UPDATE MAJORITY
    public function update(Request $request, $id)
    {

    }


    // DELETE MAJORITY
    public function destroy($id)
    {

    }
}
