<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OpportunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 5);

            $perPageOptions = [5, 10, 15, 20, 50];

            if (!in_array($perPage, $perPageOptions)) {
                $perPage = 5;
            }

            $opportunity = Opportunity::latest()->paginate($perPage);


            return response()->json([
                'success' => true,
                'message' => 'Daftar Data Opportunity',
                'data' => $opportunity
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data opportunity',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:opportunities',
            'name' => 'required|string|max:255',
            'quota' => 'required|numeric',
            'description' => 'required|string|max:255',
            'school_id' => 'required|string|max:255|exists:school,uuid',
            'mentor_id' => 'required|string|max:36|exists:users,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = $validator->validated();
            $data['opportunity_id'] = Str::uuid()->toString();
            $data['created_by'] = Auth::user()->name;
            $data['updated_by'] = Auth::user()->name;
            $opportunity = Opportunity::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Data Opportunity Berhasil Disimpan!'
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data opportunity',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'quota' => 'required|numeric',
            'description' => 'required|string|max:255',
            'school_id' => 'required|string|max:255|exists:school,uuid',
            'mentor_id' => 'required|string|max:36|exists:users,uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $data = $validator->validated();
            $data['updated_by'] = Auth::user()->name;
            $opportunity = Opportunity::where('opportunity_id', $id)->first();
            if (!$opportunity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data opportunity tidak ditemukan!',
                ], Response::HTTP_NOT_FOUND);
            }
            $opportunity->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Data Opportunity Berhasil Diupdate!'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data opportunity',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $opportunity = Opportunity::where('opportunity_id', $id)->first();
        if (!$opportunity) {
            return response()->json([
                'success' => false,
                'message' => 'Data opportunity tidak ditemukan!',
            ], Response::HTTP_NOT_FOUND);
        }
        $opportunity->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data opportunity Berhasil Dihapus!'
        ], Response::HTTP_OK);
    }
}
