<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{

    public function getProfile(Request $request)
    {
        $user = $request->user()->load('school', 'major');

        return response()->json([
            "success" => true,
            "data" => new ProfileResource($user)
        ], 200);
    }
}
