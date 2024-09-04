<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('name');
        $users = User::where('name', 'LIKE', "%$search%")->paginate(5);
        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function filterByRole(Request $request) {}

    public function store(Request $request)
    {
        // input validator
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nip' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'id_role' => 'required|exists:roles,id',
        ]);

        // create new user
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->nip = $request->input('nip');
        $user->nisn = $request->input('nisn');
        $user->id_role = $request->input('id_role');
        $user->created_by = auth()->id();  // id admin as creator
        $user->save();

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        // input validator
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => 'nullable|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'id_role' => 'required|exists:roles,id',
        ]);

        // find user
        $user = User::findOrFail($id);

        // update user data
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->nip = $request->input('nip');
        $user->nisn = $request->input('nisn');
        $user->id_role = $request->input('id_role');
        $user->updated_by = auth()->id();  // id admin as creator
        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        // find user
        $user = User::findOrFail($id);

        // set column deleted_by and soft deleting
        $user->deleted_by = auth()->id();  // id admin as creator
        if ($user->save() && $user->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User gagal dihapus'
            ], 400);
        }
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Users Imported Successfully');
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
        $users = User::all(['id', 'name', 'email', 'nip', 'nisn', 'id_role']);

        $pdf = Pdf::loadView('exportPDF.exportUsersToPDF', ['users' => $users]);

        return $pdf->download('users.pdf');
    }
}
