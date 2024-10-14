<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        // Ambil keyword dari parameter query
        $keyword = $request->get('keyword');

        // Ambil data activities, dengan pencarian jika ada keyword
        $activities = Activity::when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', "%{$keyword}%");
        })->get();

        // Mengembalikan response JSON menggunakan ActivityResource
        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ActivityResource::collection($activities),
        ]);
    }


    public function store(Request $request)
    {
        try {
            // Validasi data input
            $validated = $request->validate([
                'program_id' => 'required|exists:internships,uuid',
                'name' => 'required|max:255',
                'school_id' => 'nullable|exists:school,uuid',
                'partner_id' => 'required|exists:partners,uuid',
                'teacher_id' => [
                    'required',
                    'exists:users,uuid',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $user = User::where('uuid', $value)->first();
                            if (!$user || !$user->hasRole('Teacher')) {
                                $fail('The selected teacher is not a valid teacher.');
                            }
                        }
                    },
                ],
                'description' => 'required|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            // Buat Activity baru
            $activity = Activity::create([
                'uuid' => (string) Str::uuid(),
                'code' => Activity::generateUniqueCode(),
                'program_id' => $validated['program_id'],
                'name' => $validated['name'],
                'school_id' => $validated['school_id'] ?? null,
                'partner_id' => $validated['partner_id'],
                'teacher_id' => $validated['teacher_id'],
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'created_by' => auth()->user()->uuid,
            ]);

            // Redirect atau berikan respon sukses
            return response()->json([
                'success' => true,
                'message' => 'Data added successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap ValidationException dan kembalikan respons error yang disesuaikan
            return response()->json([
                'success' => false,
                'message' => 'Validation errors occurred.',
                'errors' => $e->errors() // Berikan pesan error spesifik
            ], 422);
        } catch (\Exception $e) {
            // Tangani error lain yang mungkin terjadi
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
