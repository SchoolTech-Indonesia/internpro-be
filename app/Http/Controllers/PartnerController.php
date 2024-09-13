<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerGetRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * Display a listing of Partners.
     *
     *
     * This method is used to get all partners or search partners by name. It can also be used to paginate the result.
     * The search parameter is optional and can be used to search for partners by name. The paginate parameter is also optional and can be used to paginate the result.
     * @param PartnerGetRequest $request
     *
     * @return JsonResponse
     */
    public function index(PartnerGetRequest $request): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }

        $query = Partner::query();

        if ($request->string('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->integer('paginate')) {
            $paginate = $request->input('paginate');
            $partners = $query->Simplepaginate($paginate);
        } else {
            $partners = $query->get();
        }

        if ($partners->isEmpty()) {
            return (new MessageResource(null, false, 'No partner found'))->response()->setStatusCode(404);
        }

        return PartnerResource::collection($partners)->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PartnerRequest $request
     *
     * @return JsonResponse
     */
    public function store(PartnerRequest $request): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

//        Storage::disk('s3')->put('/folder_name', $validatedData->file);

        try {
            $partner = Partner::create($validatedData);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to create partner', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(null, true, 'Partner has been created successfully'))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param $uuid
     * @return JsonResponse
     */
    public function show($uuid): JsonResponse
    {
        $partner = Partner::find($uuid);
        if (!$partner) {
            return (new MessageResource(null, false, 'Partner not found'))->response()->setStatusCode(404);
        }

        return (new PartnerResource($partner))->response();
    }

    /**
     * Update the specified resource in storage.
     * @param PartnerRequest $request
     * @param $uuid
     *
     * @return JsonResponse
     */
    public function update(PartnerRequest $request, $uuid): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json($request->validator->messages(), 400);
        }
        $validatedData = $request->validated();

        $partner = Partner::find($uuid);
        if (!$partner) {
            return (new MessageResource(null, false, 'Partner not found'))->response()->setStatusCode(404);
        }

        try {
            $partner->update($validatedData);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to update partner', $e->getMessage()))->response()->setStatusCode(500);
        }

        return (new MessageResource(null, true, 'Partner has been updated successfully'))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $partner = Partner::find($uuid);
        if (!$partner) {
            return (new MessageResource(null, false, 'Partner not found'))->response()->setStatusCode(404);
        }

        try {
            $partner->delete();
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to delete partner', $e->getMessage()))->response()->setStatusCode(500);
        }

        return (new MessageResource(null, true, 'Partner has been deleted successfully'))->response();
    }
}
