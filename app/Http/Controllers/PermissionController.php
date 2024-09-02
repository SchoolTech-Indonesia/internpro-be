<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Role and Permissions
 *
 * APIs for managing permissions
 */
class PermissionController extends Controller
{
    /**
     * @return JsonResponse
     *
     *  Get all permissions
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        if ($permissions->isEmpty()) {
            return response()->json([
                'message' => 'No permissions found'
            ], 404);
        }

        return response()->json($permissions, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Get permission of certain role
     */
    public function show($id): JsonResponse
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')
        ], 200);
    }

    /**
     * @param PermissionRequest $request
     * @param $id
     * @return JsonResponse
     *
     * Update permission of a role
     */
    public function update(PermissionRequest $request, $id): JsonResponse
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
