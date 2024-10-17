<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InternshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function show(Internship $internship): JsonResponse
    {
        return response()->json($internship);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
   
            // input validator
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'school_id' => 'required|string|exists:school,uuid',
                'major_ids' => 'required|array',
                'major_ids.*' => 'exists:majors,uuid',
                'class_ids' => 'required|array',
                'class_ids.*' => 'exists:classes,uuid',
                'coordinator_ids' => 'required|array',
                'coordinator_ids.*' => 'exists:users,uuid',
            ]);

            // validator for class in selected major
            $validClasses = Kelas::whereIn('major', $validatedData['major_ids'])
            ->whereIn('uuid', $validatedData['class_ids'])
            ->pluck('uuid')
            ->toArray();

            if (count($validatedData['class_ids']) !== count($validClasses)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Beberapa class tidak sesuai dengan major yang dipilih.',
                ], 422);
            }

            // filter for users with coordinator role
            $validCoordinatorIds = User::whereHas('roles', function($query) {
                $query->where('name', 'Coordinator');
            })->pluck('uuid')->toArray();

            // only get valid coordinator_ids
            $filteredCoordinatorIds = array_intersect($validatedData['coordinator_ids'], $validCoordinatorIds);

            // get school_id from auth user
            $user = auth()->user();
            $schoolId = $user->school_id;

            // create new internship program
            $internship = new Internship();
            $internship->name = $validatedData['name'];
            $internship->start_date = $validatedData['start_date'];
            $internship->end_date = $validatedData['end_date'];
            $internship->description = $validatedData['description'];
            $internship->school_id = $schoolId;
            $internship->save();

            // sync class with internship
            $internship->classes()->sync($validatedData['class_ids']);
            
            // sync major with internship
            $internship->majors()->sync($validatedData['major_ids']);

            // sync coordinator with internship
            $internship->coordinators()->sync($filteredCoordinatorIds);

            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Data internship berhasil ditambahkan',
                'data' => $internship
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Internship $internship)
    {
        try {
            DB::beginTransaction();

            // input validator
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'school_id' => 'nullable|string|exists:school,uuid',
                'major_ids' => 'nullable|array',
                'major_ids.*' => 'exists:majors,uuid',
                'class_ids' => 'nullable|array',
                'class_ids.*' => 'exists:classes,uuid',
                'coordinator_ids' => 'nullable|array',
                'coordinator_ids.*' => 'exists:users,uuid',
            ]);

            // filter for users with coordinator role
            $validCoordinatorIds = User::whereHas('roles', function($query) {
                $query->where('name', 'Coordinator');
            })->pluck('uuid')->toArray();

            // only get valid coordinator_ids
            $filteredCoordinatorIds = array_intersect($validatedData['coordinator_ids'], $validCoordinatorIds);
            
            // get school_id from auth user
            $user = auth()->user();
            $schoolId = $user->school_id;

            // create new internship program
            $internship->name = $validatedData['name'];
            $internship->start_date = $validatedData['start_date'];
            $internship->end_date = $validatedData['end_date'];
            $internship->description = $validatedData['description'];
            $internship->school_id = $schoolId;
            $internship->save();

            // sync major with internship
            if (isset($validatedData['major_ids'])) {
                $internship->majors()->sync($validatedData['major_ids']);

                // sync class with internship
                if (isset($validatedData['class_ids'])) {
                    $validClassIds = Kelas::whereIn('uuid', $validatedData['class_ids'])
                        ->whereIn('major', $validatedData['major_ids']) // Filter berdasarkan major
                        ->pluck('uuid')
                        ->toArray();
                    
                    $internship->classes()->sync($validClassIds);
                }    
            }

            // sync coordinator with internship
            if (isset($validatedData['coordinator_ids'])) {
                $internship->coordinators()->sync($filteredCoordinatorIds);
            }

            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Data internship berhasil diubah',
                'data' => $internship
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengubah data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Internship $internship)
    {
        try {
            DB::beginTransaction();

            $internship->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data internship berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
