<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Role and Permissions
 */
class RoleControllers extends Controller
{
    /**
     * @return JsonResponse
     *
     * Get all roles
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->get();

        if (count($roles) == 0) {
            return response()->json([
                'message' => 'No roles found'
            ], 404);
        }
        return response()->json($roles, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Get role by id
     */
    public function show($id): JsonResponse
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
        ], 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Get role by id
     */
    public function DeleteRole($id): JsonResponse
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

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * Create new role
     */
    public function store(Request $request): JsonResponse
    {
        error_log($request);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
            'description' => 'nullable|string',
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
