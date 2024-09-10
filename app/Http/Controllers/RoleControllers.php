<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

/**
 * @tags Roles
 */
class RoleControllers extends Controller
{
    /**
     * @return JsonResponse
     *
     * Get all roles and corresponding permissions
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('permissions')->withCount('users')->get();

        if ($roles->isEmpty()) {
            return response()->json([
                'message' => 'No roles found'
            ], 404);
        }
        return RoleResource::collection($roles)->response();
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Get role and permission by id
     */
    public function show($id): JsonResponse
    {
        $role = Role::with('permissions')->withCount('users')->find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return (new RoleResource($role))->response();
    }

    /**
     * @param $id
     * @return JsonResponse
     *
     * Delete role by id
     */
    public function destroy($id): JsonResponse
    {
        $Role = Role::with('permissions')->withCount('users')->find($id);
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
                    'message' => 'Data berhasil dihapus',
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
     * @param RoleCreateRequest $request
     * @return JsonResponse
     *
     * Create new role
     */
    public function store(RoleCreateRequest $request): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();
        // Membuat role baru
        try {
            $role = Role::create([
                'name' => $validatedData['name']
            ]);
            $role->syncPermissions($validatedData['permissions']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 400);
        }

        $roles = Role::with('permissions')->withCount('users')->find($role->id);

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully',
        ], 201);
    }

    /**
     * @param RoleUpdateRequest $request
     * @param $id
     * @return JsonResponse
     *
     * Update role name and permissions
     */
    public function update(RoleUpdateRequest $request, $id): JsonResponse
    {
        // Check if the request is valid
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        try {
            $role = Role::findById($id);
            $role->update(['name' => $validatedData['name']]);
            $role->syncPermissions($validatedData['permissions']);
        } catch (\Exception $e) {
            // create if permission name or not unique exception already exist
            if ($e->getCode() === '23000') {
                return response()->json([
                    'status' => false,
                    'message' => 'Role name already exists'
                ], 400);
            }
            return response()->json([
                'status' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 400);
        }

        $roles = Role::with('permissions')->withCount('users')->find($role->id);

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully',
        ], 201);
    }
}