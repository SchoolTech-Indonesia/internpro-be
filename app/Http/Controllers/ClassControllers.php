<?php

namespace App\Http\Controllers;

use App\Http\Resources\KelasResource;
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
            $kelas = Kelas::latest()
                ->paginate($perPage)
                ->makeHidden(['created_at', 'updated_at', 'deleted_at']);

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
            "class_name" => "required|string|max:255",
            "major" => "required|exists:majors,uuid",
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $data = $validator->validated();
            $data['created_by'] = Auth::user()->uuid;
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
            // Hapus validasi untuk 'class_code'
            "class_name" => "required|string|max:255",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // Ambil data yang telah divalidasi
            $data = $validator->validated();
            $data['updated_by'] = Auth::user()->uuid;

            // Jika 'class_code' masih perlu diambil dari database, bisa ambil sebelum update
            $kelas = Kelas::where('uuid', $id)->first();
            if ($kelas) {
                // Menjaga class_code yang ada tanpa mengubahnya
                $data['class_code'] = $kelas->class_code;
                Kelas::where('uuid', $id)->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Data Kelas Berhasil Diperbarui!',
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Kelas tidak ditemukan!',
                ], Response::HTTP_NOT_FOUND);
            }
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
            $kelas->deleted_by = Auth::user()->uuid; // Mengambil pengguna yang menghapus
            $kelas->save();
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
     * Search Kelas by name
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi Gagal',
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $data = $validator->validated();
            $class = Kelas::where('class_name', 'like', '%' . $data['search'] . '%')->get();
            if ($class->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Kelas Tidak Ditemukan!'
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'success' => true,
                'message' => 'Kelas Ditemukan!',
                'data' => KelasResource::collection($class),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // for internship management needs
    public function getClassesByMajors(Request $request)
    {
        // input validator
        $validatedData = $request->validate([
            'major_ids' => 'required|array',
            'major_ids.*' => 'exists:majors,uuid', // major id validator
        ]);

        // get classes based on the selected majors
        $classes = Kelas::whereIn('major_id', $validatedData['major_ids'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Classes retrieved successfully',
            'data' => $classes,
        ], 200);
    }
}
