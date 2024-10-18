<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\SchoolResource;
use Illuminate\Validation\Rule;

/**
 * Class SchoolControllers
 *
 * @package App\Http\Controllers
 */
class SchoolControllers extends Controller
{
    /**
     * Show all school data with pagination & searching.
     */
    public function index()
    {
        try {
            $perPage = request()->query('per_page', 5);
            $search = request()-> query('search');

            $perPageOptions = [5, 10, 15, 20, 50];
            $schools = School::query();
            if (!in_array($perPage, $perPageOptions)) {
                $perPage = 5;
            }
            if($search){
                $schools->where('school_name', 'like', '%' . $search . '%');
            }

            $schools = $schools->latest()->paginate($perPage);
            if ($schools->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Sekolah Tidak Ditemukan!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar Data Sekolah',
                'data' => $schools
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data sekolah',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new school record.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255|unique:school',
            'school_address' => 'required|string|max:255',
            'phone_number' => 'required|numeric|digits_between:11,13|unique:school',
            'start_member' => 'required|date',
            'end_member' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $school = School::create([
                 
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'phone_number' => $request->phone_number,
                'start_member' => $request->start_member,
                'end_member' => $request->end_member,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Sekolah Berhasil Disimpan!'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data sekolah',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show a school record by UUID.
     */
    public function show($uuid)
    {
        try {
            $school = School::where('uuid', $uuid)->first();

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Sekolah Tidak Ditemukan',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail Data Sekolah',
                'data' => new SchoolResource($school),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data sekolah',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a school record by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $school = School::where('uuid', $uuid)->first();

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Data Sekolah Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'school_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('school', 'school_name')->ignore($school->uuid, 'uuid')
            ],
            'school_address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'numeric',
                'digits_between:11,13',
                Rule::unique('school', 'phone_number')->ignore($school->uuid, 'uuid')
            ],
            'start_member' => 'required|date',
            'end_member' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            School::where('uuid', $uuid)->update([
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'phone_number' => $request->phone_number,
                'start_member' => $request->start_member,
                'end_member' => $request->end_member,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Sekolah Berhasil Diperbarui!',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data sekolah',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a school record by UUID (soft delete).
     */
    public function destroy($uuid)
    {
        try {
            $school = School::withTrashed()->where('uuid', $uuid)->first();

            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Sekolah Tidak Ditemukan!',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($school->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Sekolah Sudah Dihapus Sebelumnya!',
                ], Response::HTTP_NOT_FOUND);
            }

            $school->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data Sekolah Berhasil Dihapus!',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}