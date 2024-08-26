<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


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
      $school = School::latest()->paginate(5);

      return response()->json([
        'success' => true,
        'message' => 'Daftar Data Sekolah',
        'data' => $school
      ], 200);
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
          return response()->json($validator->errors(), 422);
        }

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
          'data' => $school
        ], 201);
    }

    /**
     * Show a school record by UUID.
     */
    public function show($uuid)
    {
        $school = School::where('uuid', $uuid)->first();
        return response()->json([
          'success' => true,
          'message' => 'Detail data sekolah',
          'data' => $school
        ], 200);
    }

    /**
     * Update a school record by UUID.
     */
    public function update(Request $request, $uuid)
    {
      $school = School::where('uuid', $uuid)->first();

      if (!$school) {
        return response()->json([
          'success' => true,
          'message' => 'Data Sekolah Tidak Ditemukan!',
          'data' => $school
        ], 404);
      }

      $validator = Validator::make($request->all(), [
        'school_name' => 'required|string|max:255|unique:school',
        'school_address' => 'required|string|max:255',
        'phone_number' => 'required|string|max:15|unique:school',
        'start_member' => 'required|date_format:Y-m-d H:i:s',
        'end_member' => 'required|date_format:Y-m-d H:i:s',
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
      }

      School::where('uuid', $uuid)->update([
        'school_name' => $request->school_name,
        'school_address' => $request->school_address,
        'phone_number' => $request->phone_number,
        'start_member' => $request->start_member,
        'end_member' => $request->end_member
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Data Sekolah Berhasil Diperbarui!',
        'data' => $school
      ], 200);
    }

    /**
     * Delete a school record by UUID.
     */
    public function deleteSchool($uuid)
    {
      //
    }
}
