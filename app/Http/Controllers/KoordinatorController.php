<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KoordinatorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // $search = $request->query('name');
        // $rows = $request['rows'] != 0 ? $request['rows'] : 5;
        // $roles = $request->query("roles") ? explode(',', $request->query("roles")) : [];
        // $users = User::where('name', 'LIKE', "%$search%")->when(count($roles) != 0, function ($query) use ($roles) {
        //     $query->whereIn("role_id", $roles);
        // })->paginate($rows);
        // return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        // return response()->json($user);
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

            // create new Koordinator
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->password = bcrypt($validatedData['password']);
            $user->nip = $validatedData['nip'] ?? null;
            $user->nisn = $validatedData['nisn'] ?? null;
            $user->role_id = 2; // 2 as Koordinator role_id
            $user->created_by = auth()->id(); // admin id as creator
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
            // input validator
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8|confirmed',
                'nip' => 'nullable|string|max:20',
                'nisn' => 'nullable|string|max:20',
            ]);

            // find Koordinator by id
            $user = User::where('id', $id)->where('role_id', 2)->firstOrFail();

            // update Koordinator data
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
                'message' => 'User Koordinator berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            // handling general error
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
            $user = User::where('id', $id)->where('role_id', 2)->firstOrFail();
            
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

    public function importUsers(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx'
            ]);
    
            // import process
            Excel::import(new UsersImport(2), $request->file('file'));

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

    public function exportUsersToXLSX()
    {
        return Excel::download(new UsersExport(2), 'koordinator.xlsx');
    }

    public function exportUsersToCSV()
    {
        return Excel::download(new UsersExport(2), 'koordinator.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportUsersToPDF()
    {
        $users = User::where('role_id', 2)->get(['id', 'name', 'email', 'nip', 'nisn', 'role_id']);

        $pdf = Pdf::loadView('exportPDF.exportUsersToPDF', ['users' => $users]);

        return $pdf->download('koordinator.pdf');
    }
}
