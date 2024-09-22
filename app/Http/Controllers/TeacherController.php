<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeacherResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::role('Teacher')->with('school');

            if ($request->has('name')) {
                $name = $request->query('name');
                $query->where('name', 'like', '%' . $name . '%');
            }

            $perPage = $request->query('per_page', 10);

            $teachers = $query->paginate($perPage);

            if ($teachers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No teacher data found with the provided name",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => "List teacher data",
                'data' => TeacherResource::collection($teachers->items()),
                'meta' => [
                    'current_page' => $teachers->currentPage(),
                    'last_page' => $teachers->lastPage(),
                    'per_page' => $teachers->perPage(),
                    'total' => $teachers->total(),
                ],
                'links' => [
                    'first' => $teachers->url(1),
                    'last' => $teachers->url($teachers->lastPage()),
                    'prev' => $teachers->previousPageUrl(),
                    'next' => $teachers->nextPageUrl(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was something wrong when getting all teacher data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'nip_nisn' => 'required|string|max:20|unique:users',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|unique:users,phone_number',
                'password' => 'required|string|min:8',
                'school_id' => 'required|exists:school,uuid'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $teacher = User::create([
                'nip_nisn' => $request->nip_nisn,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => bcrypt($request->password),
                'school_id' => $request->school_id,
            ]);

            $teacher->assignRole('Teacher');

            return response()->json([
                'success' => true,
                'message' => "Teacher Data Created Successfully",
                "data" => $teacher
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was something wrong when created teacher data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $uuid)
    {

        $teacher = User::role('Teacher')->where('uuid', $uuid)->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher Data Not Found!',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nip_nisn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nip_nisn')->ignore($teacher->uuid, 'uuid')
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->uuid, 'uuid')
            ],
            'phone_number' => [
                'sometimes',
                'string',
                'max:15',
                Rule::unique('users', 'phone_number')->ignore($teacher->uuid, 'uuid')
            ],
            'password' => [
                'sometimes',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {

            User::role('Teacher')->where('uuid', $uuid)->update([
                'nip_nisn' => $request->nip_nisn ?? $teacher->nip_nisn,
                'name' => $request->name ?? $teacher->name,
                'email' => $request->email ?? $teacher->email,
                'phone_number' => $request->phone_number ?? $teacher->phone_number,
                'password' => $request->password ? bcrypt($request->password) : $teacher->password,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teacher Data Updated Successfully!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was something wrong when updating teacher data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($uuid)
    {
        try {
            $teacher = User::role("Teacher")->with('school')->where("uuid", $uuid)->first();

            if (!$teacher) {
                return response()->json([
                    "success" => false,
                    "message" => "Teacher Data Not Found!",
                ], 404);
            }

            return response()->json([
                "success" => true,
                "message" => "Detailed Teacher Data",
                "data" => new TeacherResource($teacher)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was something wrong when getting detailed teacher data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($uuid)
    {
        try {
            $teacher = User::role('Teacher')->where('uuid', $uuid)->first();

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher Data Not Found!',
                ], 404);
            }

            $teacher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Teacher Data Deleted Successfully!',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
