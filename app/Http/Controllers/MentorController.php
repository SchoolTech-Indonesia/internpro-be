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
    public function index():JsonResponse
    {
        $mentor = User::role('Mentor')->get();
        if($mentor->isEmpty()){
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return MentorResource::collection($mentor)->response();
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