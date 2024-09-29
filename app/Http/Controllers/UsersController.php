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
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('name');
        $rows = $request['rows'] != 0 ? $request['rows'] : 5;
        $roles = $request->query("roles") ? explode(',', $request->query("roles")) : [];
        $users = User::where('name', 'LIKE', "%$search%")->when(count($roles) != 0, function ($query) use ($roles) { 
            $query->whereHas("roles", function ($query) use ($roles) {
                $query->whereIn("name", $roles);
            });
        })->paginate($rows);
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
                'nip_nisn' => 'required|string|max:20',
                'role' => 'required|string|exists:roles,name',
                'school_id' => 'required|exists:school,uuid',
                'major_id' => 'required|exists:majors,uuid',
                'class_id' => 'required|exists:classes,uuid',
                'partner_id' => 'required|exists:partners,uuid',
            ]);

            // create new user
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->phone_number = $validatedData['phone_number'];
            $user->password = bcrypt($validatedData['password']);
            $user->nip_nisn = $validatedData['nip_nisn'] ?? null;
            $user->created_by = auth()->id();  // admin id as creator
            $user->assignRole($validatedData['role']);
            $user->school_id = $validatedData['school_id'];
            $user->major_id = $validatedData['major_id'];
            $user->class_id = $validatedData['class_id'];
            $user->partner_id = $validatedData['partner_id'];
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dibuat',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'password' => 'nullable|string|min:8',
                'nip_nisn' => 'required|string|max:20',
                'role' => 'required|string|exists:roles,name',
                'school_id' => 'required|exists:school,uuid',
                'major_id' => 'required|exists:majors,uuid',
                'class_id' => 'required|exists:classes,uuid',
                'partner_id' => 'required|exists:partners,uuid',
            ];

            // find user by id
            $user = User::findOrFail($id);

            if ($user->email != $request['email']) {
                $rules['email'] = 'nullable|email|unique:users,email';
            } else {
                $rules['email'] = 'nullable|email';
            }

            if ($user->phone_number != $request['phone_number']) {
                $rules['phone_number'] = 'nullable|string|unique:users,phone_number';
            } else {
                $rules['phone_number'] = 'nullable|string';
            }

            // input validator
            $validatedData = $request->validate($rules);

            // update user data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->phone_number = $validatedData['phone_number'];

            if ($request->filled('password')) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->nip_nisn = $validatedData['nip_nisn'] ?? null;
            $user->assignRole($validatedData['role']);
            $user->updated_by = auth()->id(); // admin id as creator
            $user->school_id = $validatedData['school_id'];
            $user->major_id = $validatedData['major_id'];
            $user->class_id = $validatedData['class_id'];
            $user->partner_id = $validatedData['partner_id'];
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // database protection
            DB::beginTransaction();

            // find user by id
            $user = User::findOrFail($id);

            // set kolom deleted_by dan soft delete
            $user->deleted_by = auth()->id(); // admin id as deleter

            // delete user
            $user->delete();

            // commit transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            // database protection
            DB::rollBack();

            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage()
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
            Excel::import(new UsersImport, $request->file('file'));

            return response()->json([
                'status' => true,
                'message' => 'Users Imported Successfully'
            ], 200);
        } catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengimpor users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportUsersToXLSX()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function exportUsersToCSV()
    {
        return Excel::download(new UsersExport, 'users.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportUsersToPDF()
    {
        $users = User::all();

        $pdf = Pdf::loadView('exportPDF.exportUsersToPDF', ['users' => $users])->setPaper('a4', 'landscape');

        return $pdf->download('users.pdf');
    }
}
