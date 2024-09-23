<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ClassControllers extends Controller
{
    /**
     * Show all Kelas with pagination
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);
            $perPageOptions = [5, 10, 15, 20, 50];
            if (!in_array($perPage, $perPageOptions)) {
                $perPage = 5;
            }
            $kelas = Kelas::latest()->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Daftar Data Kelas',
                'data' => $kelas
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data kelas',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create Kelas
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "class_code" => "required|unique:classes|max:255",
            "class_name" => "required|string|max:255",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $data = $validator->validated();
            $data['created_by'] = Auth::user()->name;
            Kelas::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan!',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Show Kelas by ID
     */
    public function show($id)
    {
        $kelas = Kelas::where('uuid', $id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Kelas',
            'data' => $kelas
        ], Response::HTTP_OK);
    }


    /**
     * Update Kelas
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "class_code" => [
                "required",
                "max:255",
                Rule::unique('majors')->ignore($id, 'uuid')
            ],
            "class_name" => "required|string|max:255",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        try {
            $data = $validator->validated();
            $data['updated_by'] = Auth::user()->name;
            Kelas::where('uuid', $id)->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Data Kelas Berhasil Diperbarui!',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Kelas
     */
    public function destroy($id)
    {
        $kelas = Kelas::where('uuid', $id)->first();
        if ($kelas) {
            $kelas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dihapus!',
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan!',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Find Kelas by search
     */
    public function search(Request $request)
    {

    }
}
