<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SchoolControllers;
use App\Http\Controllers\GuruControllers;
use App\Http\Controllers\RoleControllers;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MajorityController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Exports\UsersExport;
use App\Http\Controllers\TeacherController;
use Maatwebsite\Excel\Facades\Excel;

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
Route::put('/resetpassword', [ResetPasswordController::class, 'store'])->name('resetpassword');

Route::middleware('auth:api')->group(function () {
    /**
     * route "/user"
     * @method "GET"
     */
    /**
     * route "/logout"
     * @method "POST"
     */
    Route::post('/logout', LogoutController::class)->name('logout');

    // USERS
    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('getallusers');
        Route::get('/{user:uuid}', [UsersController::class, 'show'])->name('getuser');
        Route::post('/create', [UsersController::class, 'store'])->name('createuser');
        Route::patch('/update/{id}', [UsersController::class, 'update'])->name('updateuser');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->name('deleteuser');
        Route::post('/import', [UsersController::class, 'importUsers'])->name('importusers');
        Route::get('/export/xlsx', [UsersController::class, 'exportUsersToXLSX'])->name('exportuserstoxlsx');
        Route::get('/export/csv', [UsersController::class, 'exportUsersToCSV'])->name('exportuserstocsv');
        Route::get('/export/pdf', [UsersController::class, 'exportUsersToPDF'])->name('exportuserstopdf');
    });


    // GET CURRENT PROFILE
    Route::get('/profile', [ProfileController::class, "getProfile"])->name('profile');

    // UPDATE PROFILE
    Route::put('/update-profile', [ProfileController::class, "updateProfile"])->name('updateprofile');

    //UPDATE PASSWORD
    Route::put("/update-password", [ProfileController::class, 'updatePassword'])->name('updatepassword');

    // SCHOOL endpoints
    Route::prefix('schools')->group(function () {
        Route::post('/create', [SchoolControllers::class, 'store'])->name('createschool');
        Route::get('/', [SchoolControllers::class, 'index'])->name('getallschool');
        Route::get('/{uuid}', [SchoolControllers::class, 'show'])->name('getspecificschool');
        Route::patch('/update/{uuid}', [SchoolControllers::class, 'update'])->name('updateschool');
        Route::delete('/{uuid}', [SchoolControllers::class, 'destroy'])->name('deleteschool');
        Route::post('/search', [SchoolControllers::class, 'search'])->name('searchschool');
    });

    // TEACHER
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('getallteacher');
        Route::post('/create', [TeacherController::class, 'store'])->name('createteacher');
        Route::get('/{uuid}', [TeacherController::class, 'show'])->name('getspecificteacher');
        Route::put('/update/{uuid}', [TeacherController::class, 'update'])->name('updateteacher');
        Route::delete('/{uuid}', [TeacherController::class, 'destroy'])->name('deleteteacher');
    });

    // ROLE
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleControllers::class, 'index'])->name('index');
        Route::get('/{id}', [RoleControllers::class, 'show'])->name('show');
        Route::put('/update/{id}', [RoleControllers::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleControllers::class, 'destroy'])->name('destroy');
        Route::post('/create', [RoleControllers::class, 'store'])->name('store');
    });

    // PERMISSION
    Route::prefix('permission')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/{id}', [PermissionController::class, 'show'])->name('show');
        // Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        // Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        // Route::post('/create', [PermissionController::class, 'store'])->name('store');
    });

    // MAJORITY
    Route::prefix('majority')->group(function () {
        Route::get('/', [MajorityController::class, 'index'])->name('index');
        Route::get('/{id}', [MajorityController::class, 'show'])->name('show');
        Route::put('/update/{id}', [MajorityController::class, 'update'])->name('update');
        Route::delete('/{id}', [MajorityController::class, 'destroy'])->name('destroy');
        Route::post('/create', [MajorityController::class, 'store'])->name('store');
    });

    // ADMIN
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/{uuid}', [AdminController::class, 'showAdmin'])->name('showadmin');
        Route::post('/create', [AdminController::class, 'createAdmin'])->name('createadmin');
        Route::delete('/{uuid}', [AdminController::class, 'deleteAdmin'])->name('deleteadmin');
        Route::put('/{uuid}', [AdminController::class, 'updateAdmin'])->name('updateAdmin');
    });
});
