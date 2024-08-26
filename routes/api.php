<?php

use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\GuruControllers;
use App\Http\Controllers\PermissionControllers;
use App\Http\Controllers\RoleControllers;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// AUTH
// Route::post('/register', RegisterController::class)->name('register');
Route::post('/login', LoginController::class)->name('login');
Route::post('/forgetpassword', [AuthControllers::class, 'generateOtp'])->name('forgetpassword');
Route::post('/verifyotp', [AuthControllers::class, 'verifyOtp'])->name('verifyotp');
Route::put('/resetpassword', [AuthControllers::class, 'resetPassword'])->name('resetpassword');

Route::middleware('auth:api')->group(function () {
    /**
     * route "/user"
     * @method "GET"
     */
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    /**
     * route "/logout"
     * @method "POST"
     */
    Route::post('/logout', LogoutController::class)->name('logout');

    // GURU
    Route::prefix('guru')->group(function () {
        Route::post('/create', [GuruControllers::class, 'createGuru'])->name('createguru');
        Route::get('/', [GuruControllers::class, 'getAllGuru'])->name('getallguru');
        Route::post('/update/{id}', [GuruControllers::class, 'updateGuru'])->name('updateguru');
        Route::delete('/{id}', [GuruControllers::class, 'DeleteGuru'])->name('DeleteGuru');
    });

    // ROLE
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleControllers::class, 'getAllRoles'])->name('getallroles');
        Route::get('/{id}', [RoleControllers::class, 'getSpecificRole'])->name('getspecificrole');
        Route::delete('/{id}', [RoleControllers::class, 'DeleteRole'])->name('DeleteRole');
    });

    // PERMISSION
    Route::prefix('permission')->group(function () {
        Route::post('/create', [PermissionControllers::class, 'createPermission'])->name('createpermission');
        Route::post('/update/{id}', [PermissionControllers::class, 'editPermission'])->name('editpermission');
        Route::delete('/{id}', [PermissionControllers::class, 'DeletePermission'])->name('DeletePermission');
    });
});
