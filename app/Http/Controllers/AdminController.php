<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\ProfileResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Ambil keyword untuk pencarian dari query string, jika ada
        $keyword = $request->query('keyword');

        // Ambil jumlah item per halaman dari request, default 10
        $perPage = $request->query('per_page', 10);

        // Query dasar untuk mengambil admin berdasarkan peran
        $query = User::role('Administrator');

        // Jika ada keyword, tambahkan kondisi pencarian berdasarkan nama
        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // Jika ada parameter pagination, lakukan paginasi
        if ($request->has('per_page')) {
            $admins = $query->paginate($perPage);
            // Bungkus items dalam AdminResource
            $admins->getCollection()->transform(function ($admin) {
                return new AdminResource($admin);
            });
        } else {
            // Jika tidak ada parameter pagination, ambil semua data dan bungkus dalam AdminResource
            $admins = AdminResource::collection($query->get());
        }

        // Return data sesuai dengan hasil query
        return response()->json([
            'success' => true,
            'data' => $admins,
        ], 200);
    }

    public function showAdmin($uuid)
    {
        // Cari user berdasarkan UUID
        $admin = User::where('uuid', $uuid)->firstOrFail();

        // Pastikan user memiliki role Admin
        if ($admin->hasRole('Administrator')) {
            return response()->json([
                "success" => true,
                "data" => new AdminResource($admin),
            ], 200);
        }

        return response()->json([
            "success" => false,
            "message" => "User is not an admin"
        ], 403);
    }
    public function deleteAdmin($uuid)
    {
        // Cari user berdasarkan UUID
        $admin = User::where('uuid', $uuid)->firstOrFail();

        // Pastikan user memiliki role Admin
        if ($admin->hasRole('Administrator')) {
            // Hapus admin
            $admin->delete();

            return response()->json([
                "success" => true,
                "message" => "Data deleted successfully!",
            ], 200);
        }

        return response()->json([
            "success" => false,
            "message" => "User is not an admin"
        ], 403);
    }

    public function updateAdmin(Request $request, $uuid)
    {
        $admin = User::where('uuid', $uuid)->firstOrFail();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nip_nisn' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'nip_nisn')->ignore($admin), // Abaikan email user saat ini
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($admin), // Abaikan email user saat ini
            ],
            'phone_number' => [
                'nullable',
                'string',
                Rule::unique('users', 'phone_number')->ignore($admin), // Abaikan nomor telepon user saat ini
            ],
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari admin berdasarkan UUID
        $admin = User::where('uuid', $uuid)->firstOrFail();

        // Pastikan user memiliki role Admin
        if (!$admin->hasRole('Administrator')) {
            return response()->json([
                "success" => false,
                "message" => "User is not an admin"
            ], 403);
        }

        // Update data admin
        $admin->name = $request->input('name');
        $admin->nip_nisn = $request->input('nip_nisn');
        if ($request->filled('email')) {
            $admin->email = $request->input('email');
        }
        if ($request->filled('phone_number')) {
            $admin->phone_number = $request->input('phone_number');
        }
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Done, data updated!',
            'data' => new AdminResource($admin)
        ], 200);
    }

    public function createAdmin(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nip_nisn' => 'required|string|unique:users|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->input('name'),
            'nip_nisn' => $request->input('nip_nisn'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Assign role admin
        $role = Role::findByName('Administrator');
        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => 'Data added successfully'
        ], 201);
    }

}