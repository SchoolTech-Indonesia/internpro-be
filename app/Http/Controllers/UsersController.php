<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\CreatedBy;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Maatwebsite\Excel\Facades\Excel;

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
        
        return response()->json([
            'success' => true,
            'message' => 'List of users',
            'data' => UserResource::collection($users)
        ], 200);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:users,email',
                'phone_number' => 'nullable|string|unique:users,phone_number',
                'password' => 'required|string|min:8',
                'nip_nisn' => 'required|string|max:20',
                'role_id' => 'required|exists:roles,id',
            ]);

            $rules = [];

            // role-based validation
            $role = Role::find($request->role_id);

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role tidak valid'
                ], 400);
            }

            switch ($role->name) {
                case 'Coordinator':
                    $rules['major_id'] = 'required|exists:majors,uuid';
                    $rules['class_id'] = 'sometimes|exists:classes,uuid';
                    break;

                case 'Student':
                    $rules['major_id'] = 'required|exists:majors,uuid';
                    $rules['class_id'] = 'required|exists:classes,uuid';
                    break;

                case 'Super Administrator':
                case 'Administrator':
                case 'Teacher':
                case 'Mentor':
                    $rules['major_id'] = 'sometimes|exists:majors,uuid';
                    $rules['class_id'] = 'sometimes|exists:classes,uuid';
                    break;

                default:
                    return response()->json([
                        'status' => false,
                        'message' => 'Role tidak valid'
                    ], 400);
            }

            // validate request with the rules
            $validatedRoleData = $request->validate($rules);

            // merge default validation with role-based rules
            $validatedData = array_merge($validatedData, $validatedRoleData);

            // create new user
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'] ?? null;
            $user->phone_number = $validatedData['phone_number'] ?? null;
            $user->password = bcrypt($validatedData['password']);
            $user->nip_nisn = $validatedData['nip_nisn'] ?? null;
            $user->created_by = auth()->id();  // admin id as creator
            $user->assignRole($validatedData['role_id']);
            $user->school_id = auth()->user()->school_id;
            $user->major_id = $validatedData['major_id'] ?? null;
            $user->class_id = $validatedData['class_id'] ?? null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dibuat'
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
                'role' => 'required|string|in:Super Administrator,Administrator,Coordinator,Teacher,Mentor,Student',
            ];

            // find user by id
            $user = User::findOrFail($id);

            // check if email or phone number is updated
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

            // role-based validation
            switch ($request->role) {
                case 'Coordinator':
                    $rules['school_id'] = 'nullable|exists:school,uuid';
                    $rules['major_id'] = 'required|exists:majors,uuid';
                    $rules['class_id'] = 'nullable|exists:classes,uuid';
                    break;

                case 'Student':
                    $rules['school_id'] = 'nullable|exists:school,uuid';
                    $rules['major_id'] = 'required|exists:majors,uuid';
                    $rules['class_id'] = 'required|exists:classes,uuid';
                    break;

                // no additional validation for the roles below, use default rule
                case 'Super Administrator':
                case 'Administrator':
                case 'Teacher':
                case 'Mentor':
                    // no special rules, use default rules, or add if any
                    $rules['school_id'] = 'nullable|exists:school,uuid';
                    $rules['major_id'] = 'nullable|exists:majors,uuid';
                    $rules['class_id'] = 'nullable|exists:classes,uuid';
                    break;

                default:
                    // handling invalid role
                    return response()->json([
                        'status' => false,
                        'message' => 'Role tidak valid'
                    ], 400);
            }

            // input validator
            $validatedData = $request->validate($rules);

            // update user data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'] ?? null;
            $user->phone_number = $validatedData['phone_number'] ?? null;

            if ($request->filled('password')) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->nip_nisn = $validatedData['nip_nisn'] ?? null;
            $user->assignRole($validatedData['role']);
            $user->school_id = $validatedData['school_id'];
            $user->major_id = $validatedData['major_id'] ?? null;
            $user->class_id = $validatedData['class_id'] ?? null;
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
            // find user by id
            $user = User::findOrFail($id);

            // check if the auth user has the same school_id
            if (auth()->user()->school_id !== $user->school_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden: You are not authorized to delete this user.'
                ], 403);
            }

            // database protection
            DB::beginTransaction();

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