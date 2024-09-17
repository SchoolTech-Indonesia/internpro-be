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
use Illuminate\Validation\Rule;

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
            ], 201);
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
        $validator = Validator::make($request->all(), [
            "major_code" => [
                "required",
                "max:255",
                Rule::unique('majors')->ignore($id, 'uuid')
            ],
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
            $data['updated_by'] = $user->name;
            Major::where('uuid', $id)->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Major updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // DELETE MAJORITY
    public function destroy($id)
    {
        $major = Major::where('uuid', $id)->first();
        if ($major) {
            $major->delete();
            return response()->json([
                'success' => true,
                'message' => 'Major deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Major not found',
            ], 404);
        }
    }
}
