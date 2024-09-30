<?php

namespace App\Http\Controllers;

use App\Http\Resources\MajorResource;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MajorityController extends Controller
{
    // GET MAJORITY
    public function index()
    {
        try {
            // $perPage = request()->get('per_page', 5);

            // $perPageOptions = [5, 10, 15, 20, 50];

            // if (!in_array($perPage, $perPageOptions)) {
            //     $perPage = 5;
            // }

            $major = Major::latest()->paginate(5);


            return response()->json([
                'success' => true,
                'message' => 'Daftar Data Major',
                'data' => $major
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data major',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            $data['created_by'] = Auth::user()->name;
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
                Rule::unique('majors')->ignore($id, 'uuid'),
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
            $data['updated_by'] = Auth::user()->name;
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

    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $major = Major::where('major_name', 'like', '%' . $request->search . '%')->get();

            if ($major->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Major Tidak Ditemukan!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Major Sekolah Ditemukan!',
                'data' => MajorResource::collection($major),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan Saat Mencari Major',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
