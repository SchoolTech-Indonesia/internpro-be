<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SchoolControllers
 *
 * @package App\Http\Controllers
 */
class SchoolControllers extends Controller
{
    /**
     * Show all school data with pagination.
     */
    public function index()
    {
        try {
            $schools = School::latest()->paginate(5);

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
            'phone_number' => 'required|string|max:15|unique:school',
            'start_member' => 'required|date_format:Y-m-d H:i:s',
            'end_member' => 'required|date_format:Y-m-d H:i:s',
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
                'uuid' => Str::uuid()->toString(),
                'school_name' => $request->school_name,
                'school_address' => $request->school_address,
                'phone_number' => $request->phone_number,
                'start_member' => $request->start_member,
                'end_member' => $request->end_member,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Sekolah Berhasil Ditambahkan!',
                'data' => $school,
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
                'data' => $school,
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
            'school_name' => 'required|string|max:255|unique:school,school_name,' . $school->uuid . ',uuid',
            'school_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:school,phone_number,' . $school->uuid . ',uuid',
            'start_member' => 'required|date_format:Y-m-d H:i:s',
            'end_member' => 'required|date_format:Y-m-d H:i:s',
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
            $school = School::where('uuid', $uuid)->first();
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Sekolah Tidak Ditemukan!',
                ], Response::HTTP_NOT_FOUND);
            } else {
                School::where('uuid', $uuid)->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Data Sekolah Berhasil Dihapus!',
                ], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Search school data.
     */
    public function search(Request $request)
    {
        try {
            $school = School::where('school_name', 'like', '%' . $request->search . '%')->get();
            if ($school->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Sekolah Tidak Ditemukan!'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Sekolah Ditemukan!',
                'data' => $school
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data sekolah',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
