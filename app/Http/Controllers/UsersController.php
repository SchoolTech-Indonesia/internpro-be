<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    public function createUser(Request $request)
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

    public function updateUser(Request $request, $id)
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

    public function deleteUser($id)
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
}