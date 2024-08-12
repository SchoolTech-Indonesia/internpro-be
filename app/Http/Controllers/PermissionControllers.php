<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionControllers extends Controller
{
    public function createPermission(Request $request)
    {
        $permission = new Permission();
        Request()->validate([
            'name' => 'required|string', 
        ]);

        if (!$request->name) {
            return response()->json(['message' => 'Name is required'], 400);
        }

        $permission->name = $request->name;


        $permission->save();
        return response()->json($permission, 200);
    }

    public function editPermission(Request $request, $id)
    {
        $permission = Permission::find($id);
        $request->validate([
            'name' => 'required|string',
        ]);

        if (!$request->name) {
            return response()->json(['message' => 'Name is required'], 400);
        }

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->name = $request->name;
        $permission->save();
        return response()->json($permission, 200);
    }
    public function DeletePermission($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($permission->delete()) {
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
