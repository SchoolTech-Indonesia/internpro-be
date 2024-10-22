<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\KoordinatorExport;
use App\Imports\KoordinatorImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KoordinatorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('name');
        $rows = $request['rows'] != 0 ? $request['rows'] : 5;
        $classes = $request->query("classes") ? explode(',', $request->query("classes")) : [];

        $users = User::whereHas("roles", function ($query) {
            $query->where("name", "Coordinator");
        })
            ->where('name', 'LIKE', "%$search%")
            ->when(count($classes) != 0, function ($query) use ($classes) {
                $query->whereIn("class_id", $classes);
            })
            ->paginate($rows);
        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        try {
            // input validator
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'phone_number' => 'nullable|string|unique:users,phone_number',
                'password' => 'required|string|min:8',
                'nip_nisn' => 'nullable|string|max:20',
                'role' => 'required|string|in:Coordinator',
                'school_id' => 'nullable|exists:school,uuid',
                'major_id' => 'required|exists:majors,uuid',
                'class_id' => 'nullable|exists:classes,uuid',
                'partner_id' => 'nullable|exists:partners,uuid',
            ]);

            // create new Koordinator
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'] ?? null;
            $user->phone_number = $validatedData['phone_number'] ?? null;
            $user->password = bcrypt($validatedData['password']);
            $user->nip_nisn = $validatedData['nip_nisn'] ?? null;
            $user->created_by = auth()->id(); // admin id as creator
            $user->assignRole('Coordinator');
            $user->school_id = $validatedData['school_id'] ?? null;
            $user->major_id = $validatedData['major_id'];
            $user->class_id = $validatedData['class_id'] ?? null;
            $user->partner_id = $validatedData['partner_id'] ?? null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Koordinator berhasil dibuat',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat user Koordinator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'password' => 'sometimes|nullable|string|min:8|confirmed',
                'nip_nisn' => 'sometimes|nullable|string|max:20',
                'school_id' => 'sometimes|nullable|exists:school,uuid',
                'major_id' => 'sometimes|nullable|exists:majors,uuid',
                'class_id' => 'sometimes|nullable|exists:classes,uuid',
                'partner_id' => 'sometimes|nullable|exists:partners,uuid',
            ]);

            $user = User::role('Coordinator')->where('uuid', $id)->firstOrFail();

            $user->fill($validatedData);

            if ($request->filled('password')) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->assignRole('Coordinator');

            $user->updated_by = auth()->id();

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Koordinator berhasil diperbarui',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui user Koordinator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // database protection
            DB::beginTransaction();

            // find Koordinator by id
            $user = User::where('id', $id)->where('role', 'Coordinator')->firstOrFail();

            // set kolom deleted_by dan soft delete
            $user->deleted_by = auth()->id(); // admin id as deleter

            // delete Koordinator
            $user->delete();

            // commit transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User Koordinator berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            // database protection
            DB::rollBack();

            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus user Koordinator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importKoordinator(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx'
            ]);
            // import process
            Excel::import(new KoordinatorImport(), $request->file('file'));

            return response()->json([
                'status' => true,
                'message' => 'Users Koordinator berhasil diimpor'
            ], 200);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengimpor users Koordinator: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportKoordinatorToXLSX()
    {
        return Excel::download(new KoordinatorExport(), 'coordinator.xlsx');
    }

    public function exportKoordinatorToCSV()
    {
        return Excel::download(new KoordinatorExport(), 'coordinator.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportKoordinatorToPDF()
    {
        $users = User::where('role', 'Coordinator')->get();

        $pdf = Pdf::loadView('exportPDF.exportUsersToPDF', ['users' => $users]);

        return $pdf->download('coordinator.pdf');
    }

    // for internship management needs
    public function getCoordinatorsByMajors(Request $request)
    {
        // input validator
        $validatedData = $request->validate([
            'major_ids' => 'required|array',
            'major_ids.*' => 'exists:majors,uuid', // major id validator
        ]);

        // get coordinators based on the selected majors
        $coordinators = User::whereHas('roles', function ($query) {
            $query->where('name', 'Coordinator');
        })->whereIn('major_id', $validatedData['major_ids'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Coordinators retrieved successfully',
            'data' => $coordinators,
        ], 200);
    }
}
