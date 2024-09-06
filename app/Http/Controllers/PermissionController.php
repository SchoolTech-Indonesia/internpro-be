<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

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
     * Get a certain permission
     */
    public function show($id): JsonResponse
    {
        try {
            $permission = Permission::findById($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Permission not found'
            ], 404);
        }

        return (new PermissionResource($permission))->response();
    }

    /**
     * @param PermissionUpdateRequest $request
     * @param $id
     * @return JsonResponse
     *
     * Update existing permission name
     */
    public function update(PermissionUpdateRequest $request, $id): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        try {
            $permission = Permission::findById($id);
            $permission->update(['name' => $validatedData['permission']]);
            $updatedPermission = Permission::findById($id);
        } catch (\Exception $e) {
            // create if permission name or not unique exception already exist
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Permission name already exists'
                ], 400);
            }
            return response()->json([
                'message' => 'Failed to update permission',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Permissions updated successfully',
            'permissions' => new PermissionResource($updatedPermission)
        ], 200);
    }

    /**
     * @param PermissionCreateRequest $request
     * @return JsonResponse
     *
     * Create a new permission
     */

    public function store(PermissionCreateRequest $request): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        $createdPermissions = collect();

        try {
            foreach ($validatedData['permissions'] as $permissionData) {
                $createdPermissions->add(Permission::create($permissionData));
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create permission',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => PermissionResource::collection($createdPermissions)
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
                'data' => new PermissionResource($permission)
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete permission'
        ], 400);
    }
}