<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MentorResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Mentor
 */
class MentorController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * Display a listing of the resource.
     */
    public function index(Request $request):JsonResponse
    {
        $perPage = request()->get('per_page', 5);

        $perPageOptions = [5, 10, 15, 20, 50];

            if (!in_array($perPage, $perPageOptions)) {
                $perPage = 5;
            }
        $mentor = User::role('Mentor')->paginate($perPage);
        if($mentor->isEmpty()){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => MentorResource::collection($mentor)
        ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validateData = $request->validate([
                'nip_nisn' => 'required|string|max:20',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|string|max:15|unique:users',
                'school_id' => 'required|exists:school,uuid'
            ]);
            $user = new User();
            $user->nip_nisn = $validateData['nip_nisn'];
            $user->name = $validateData['name'];
            $user->email = $validateData['email'];
            $user->password = bcrypt($validateData['password']);
            $user->phone_number = $validateData['phone_number'];
            $user->school_id = $validateData['school_id'];
            $user->assignRole(['Mentor']);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dibuat',
            ], 201);
        }
        catch (\Exception $e) {
            // handling general error
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat mentor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param $uuid
     * @return JsonResponse
     * Display the specified resource.
     */
    public function show($uuid)
    {
        try{
            $mentor = User::role('Mentor')->where('uuid', $uuid)->first();
            if (!$mentor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Mentor Tidak Ditemukan',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail Data Mentor',
                'data' => new MentorResource($mentor),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data mentor',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
            
    }

    /**
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $mentor = User::role('Mentor')->where('uuid', $uuid)->first();
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Data Mentor Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }

        try{
            $validateData = $request->validate([
                'nip_nisn' => 'required|string|max:20',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|string|max:15|unique:users',
                'school_id' => 'required|exists:school,uuid'
            ]);

            $mentor->nip_nisn = $validateData['nip_nisn'];
            $mentor->name = $validateData['name'];
            $mentor->email = $validateData['email'];
            if ($request->filled('password')) {
                $mentor->password = bcrypt($validateData['password']);
            }
            $mentor->phone_number = $validateData['phone_number'];
            $mentor->school_id = $validateData['school_id'];
            $mentor->save();

            return response()->json([
                'success' => true,
                'message' => 'Data Mentor Berhasil Diupdate',
                ], Response::HTTP_OK);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Data Mentor Gagal Diupdate',
                ], Response::HTTP_BAD_REQUEST);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}