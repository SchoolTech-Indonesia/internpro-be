<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleControllers extends Controller
{
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
        $roleWithPermissions = DB::select("
            SELECT roles.name as role, roles.description, permissions.name
            FROM roles
            LEFT JOIN roles_permissions ON roles.id = roles_permissions.id_role
            LEFT JOIN permissions ON roles_permissions.id_permission = permissions.id
            WHERE roles.id = :id
        ", ['id' => $id]);

        if (empty($roleWithPermissions)) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        $formattedRole = [
            'role' => $roleWithPermissions[0]->role,
            'description' => $roleWithPermissions[0]->description,
            'permissions' => [],
        ];

        foreach ($roleWithPermissions as $item) {
            $formattedRole['permissions'][] = $item->name;
        }

        return response()->json([$formattedRole], 200);
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
    }
}
