<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleControllers extends Controller
{
    public function listRoles() : JsonResponse
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            return response()->json([
                'message' => 'No roles found'
            ], 404);
        }

        return response()->json($roles, 200);
    }

    public function getAllRoles()
    {
        $roles = Role::all();

        foreach($roles as $role) {
            $numberOfUsers = 0;
            $numberOfUsers += User::where('id_role', $role->id)->count();
            $role->number_of_users = $numberOfUsers;
        }

        if (count($roles) == 0) {
            return response()->json([
                'message' => 'No roles found'
            ], 404);
        }
        return response()->json($roles, 200);
    }

    public function getSpecificRole($id)
    {
        $role = Role::with('permissions:name')->find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'permissions' => $role->permissions->pluck('name')
        ],200);
    }
    public function DeleteRole($id)
    {
        $Role = Role::find($id);
        if (!$Role) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        try {
            $Role->permissions()->detach();

            if ($Role->delete()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createRole(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        // Membuat role baru
        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $request->input('description'),
        ]);

        $permissions = Permission::whereIn('name', $validatedData['permissions'])->get();

        $role->permissions()->sync($permissions);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role,
            'permissions' => $role->permissions,
        ], 201);
    }
}
