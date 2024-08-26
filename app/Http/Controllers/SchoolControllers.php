<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

/**
 * Class SchoolControllers
 *
 * @package App\Http\Controllers
 */
class SchoolControllers extends Controller
{
    /**
     * Create a new school record.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function createSchool(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|unique:school,uuid',
            'school_name' => 'required|string|max:255|unique:school,school_name',
            'school_address' => 'required|string|max:255|unique:school,school_address',
            'phone_number' => 'required|string|max:15|unique:school,phone_number',
            'start_member' => 'required|date',
            'end_member' => 'required|date|after:start_member',
        ]);

        // Save data
        $school = new School();
        $school->uuid = $request->uuid;
        $school->school_name = $request->school_name;
        $school->school_address = $request->school_address;
        $school->phone_number = $request->phone_number;
        $school->start_member = $request->start_member;
        $school->end_member = $request->end_member;
        $school->save();

        return response()->json($school, 201);
    }

    /**
     * Get all school records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllSchools()
    {
        $schools = School::all();

        if ($schools->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Daftar sekolah kosong!',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menampilkan daftar sekolah!',
            'data' => $schools
        ], 200);
    }

    /**
     * Update an existing school record by UUID.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSchool(Request $request, $uuid)
    {
      // Find school by UUID
      $school = School::where('uuid', $uuid)->first();

      if (!$school) {
          return response()->json([
              'status' => false,
              'message' => 'Data sekolah tidak ditemukan!',
          ], 404);
      }

      $request->validate([
        'school_name' => 'required|string|max:255|unique:school,school_name',
        'school_address' => 'required|string|max:255|unique:school,school_address',
        'phone_number' => 'required|string|max:15|unique:school,phone_number',
        'start_member' => 'required|date',
        'end_member' => 'required|date|after:start_member',
      ]);

      // Update data
      $school->school_name = $request->school_name;
      $school->school_address = $request->school_address;
      $school->phone_number = $request->phone_number;
      $school->start_member = $request->start_member;
      $school->end_member = $request->end_member;
      $school->save();

      return response()->json([
        'status' => true,
        'message' => 'Berhasil memperbarui data sekolah!',
        'data' => $school
      ], 200);
    }

    /**
     * Delete an existing school record by UUID.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSchool($uuid)
    {
      //
    }
}
