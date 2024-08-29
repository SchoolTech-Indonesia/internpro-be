<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function listPermissions(): JsonResponse
    {
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            return response()->json([
                'message' => 'No permissions found'
            ], 404);
        }

        return response()->json($permissions, 200);
    }

    public function getPermissionsOfRole($id): JsonResponse
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions
        ], 200);
    }

    public function editPermissionsOfRole(PermissionRequest $request, $id): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        $permissions = Permission::whereIn('name', $validatedData['permissions'])->get();

        $role->permissions()->sync($permissions);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role,
            'permissions' => $role->permissions
        ], 200);
    }
}
