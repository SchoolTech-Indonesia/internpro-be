<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Permissions
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

        return PermissionResource::collection($permissions)->response();
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
            'permissions' => PermissionResource::collection($role->permissions)
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
            'permissions' => PermissionResource::collection($role->permissions)
        ], 200);
    }

    /**
     * @param PermissionRequest $request
     * @param $id
     * @return JsonResponse
     *
     * Create a new permission
     */

    public function store(CreatePermissionRequest $request): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        $createdPermissions[] = [];
        foreach ($validatedData['permissions'] as $permissionData) {
            $createdPermissions = Permission::firstOrCreate($permissionData);
        }

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $createdPermissions
        ], 201);
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Delete a permission by id
     */
    public function destroy($id): JsonResponse
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'message' => 'Permission not found'
            ], 404);
        }

        if ($permission->delete()) {
            return response()->json([
                'message' => 'Permission deleted successfully',
                'permission' => new PermissionResource($permission)
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete permission'
        ], 400);
    }
}
