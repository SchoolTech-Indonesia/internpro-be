<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\User;
use App\Http\Resources\MajorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class MajorityController extends Controller
{
    /**
     * Show all majority with pagination.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);
            $perPageOptions = [5, 10, 15, 20, 50];
            if (!in_array($perPage, $perPageOptions)) {
                $perPage = 5;
            }
            $major = Major::latest()->paginate($perPage);
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

    /**
     * Create a Majority
     */
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $data = $validator->validated();
            $data['created_by'] = Auth::user()->name;
            Major::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Major berhasil ditambahkan!',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show a majority by ID
     */
    public function show($id)
    {
        $major = Major::where('uuid', $id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Major',
            'data' => $major
        ], Response::HTTP_OK);
    }


    /**
     * Update a majority by ID
     */
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        try {
            $data = $validator->validated();
            $data['updated_by'] = Auth::user()->name;
            Major::where('uuid', $id)->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Data Major berhasil diperbarui!',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a majority by ID
     */
    public function destroy($id)
    {
        $major = Major::where('uuid', $id)->first();
        if ($major) {
            $major->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data Major berhasil dihapus!',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Major Tidak Ditemukan!',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Search majority by name
     */
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
