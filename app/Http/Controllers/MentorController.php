<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MentorResource;

/**
 * @tags Mentor
 */
class MentorController extends Controller
{
    /**
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}