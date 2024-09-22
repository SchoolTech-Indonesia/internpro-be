<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\StudentExport;
use App\Imports\StudentImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('name');
        $rows = $request['rows'] != 0 ? $request['rows'] : 5;
        $majors = $request->query("majors") ? explode(',', $request->query("majors")) : [];
        $classes = $request->query("classes") ? explode(',', $request->query("classes")) : [];
        
        $users = User::whereHas("roles", function($query) {
            $query->where("name", "Student");
        })
        ->where('name', 'LIKE', "%$search%")
        ->when(count($majors) != 0, function ($query) use ($majors) { 
                $query->whereIn("major_id", $majors);
        })
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
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'nip' => 'nullable|string|max:20',
                'nisn' => 'nullable|string|max:20',
            ]);

            // create new Student
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->nip = $validatedData['nip'] ?? null;
            $user->nisn = $validatedData['nisn'] ?? null;
            $user->role_id = 3; // 3 as Student role_id
            $user->created_by = auth()->id(); // admin id as creator
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Student berhasil dibuat',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat user Student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // input validator
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'nip' => 'nullable|string|max:20',
                'nisn' => 'nullable|string|max:20',
            ]);

            // find Student by id
            $user = User::where('id', $id)->where('role_id', 3)->firstOrFail();

            // update Student data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if ($request->filled('password')) {
                $user->password = bcrypt($validatedData['password']);
            }
            $user->nip = $validatedData['nip'] ?? null;
            $user->nisn = $validatedData['nisn'] ?? null;
            $user->updated_by = auth()->id(); // admin id as updater
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Student berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui user Student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // database protection
            DB::beginTransaction();

            // find Stuudent by id
            $user = User::where('id', $id)->where('role_id', 3)->firstOrFail();
            
            // set kolom deleted_by dan soft delete
            $user->deleted_by = auth()->id(); // admin id as deleter
            
            // delete Student
            $user->delete();

            // commit transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User Student berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            // database protection
            DB::rollBack();

            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus user Student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importStudent(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx'
            ]);
    
            // import process
            Excel::import(new StudentImport(), $request->file('file'));

            return response()->json([
                'status' => true,
                'message' => 'Users Student berhasil diimpor'
            ], 200);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengimpor users Student: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportStudentToXLSX()
    {
        return Excel::download(new StudentExport(), 'student.xlsx');
    }

    public function exportStudentToCSV()
    {
        return Excel::download(new StudentExport(), 'student.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportStudentToPDF()
    {
        $users = User::where('role_id', 3)->get(['id', 'name', 'email', 'nip', 'nisn', 'role_id']); // 3 as Student role_id

        $pdf = Pdf::loadView('exportPDF.exportUsersToPDF', ['users' => $users]);

        return $pdf->download('student.pdf');
    }
}
