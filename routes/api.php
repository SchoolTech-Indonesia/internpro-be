<?php

use App\Exports\UsersExport;
use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ProfileResource;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthControllers;
use App\Http\Controllers\GuruControllers;
use App\Http\Controllers\KoordinatorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoleControllers;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolControllers;
use App\Http\Controllers\MajorityController;
use App\Http\Controllers\ClassControllers;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

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

        // TEACHERS
        Route::prefix("teachers")->group(function () {
            Route::get('/', [TeacherController::class, 'index'])->name('getallteacher');
            Route::post('/create', [TeacherController::class, 'store'])->name('createteacher');
            Route::get('/{uuid}', [TeacherController::class, 'show'])->name('getspecificteacher');
            Route::put('/update/{uuid}', [TeacherController::class, 'update'])->name('updateteacher');
            Route::delete('/{uuid}', [TeacherController::class, 'destroy'])->name('deleteteacher');
        });

        // ADMIN
        Route::prefix('admins')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/{uuid}', [AdminController::class, 'showAdmin'])->name('showadmin');
            Route::post('/create', [AdminController::class, 'createAdmin'])->name('createadmin');
            Route::delete('/{uuid}', [AdminController::class, 'deleteAdmin'])->name('deleteadmin');
            Route::put('/update/{uuid}', [AdminController::class, 'updateAdmin'])->name('updateAdmin');
        });

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
        Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('/{id}', [PermissionController::class, 'show'])->name('permission.show');
        // Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        // Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        // Route::post('/create', [PermissionController::class, 'store'])->name('store');
    });

    // MAJORITY
    Route::prefix('majority')->group(function () {
        Route::get('/', [MajorityController::class, 'index'])->name('majority.index');
        Route::get('/getmajor', [MajorityController::class, 'majorityShow'])->name('majorityshow');
        Route::get('/{id}', [MajorityController::class, 'show'])->name('majority.show');
        Route::put('/update/{id}', [MajorityController::class, 'update'])->name('majority.update');
        Route::delete('/{id}', [MajorityController::class, 'destroy'])->name('majority.destroy');
        Route::post('/create', [MajorityController::class, 'store'])->name('majority.store');
        Route::post('/search', [MajorityController::class, 'search'])->name('majority.search');
    });

    // ADMIN
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/{uuid}', [AdminController::class, 'showAdmin'])->name('showadmin');
        Route::post('/create', [AdminController::class, 'createAdmin'])->name('createadmin');
        Route::delete('/{uuid}', [AdminController::class, 'deleteAdmin'])->name('deleteadmin');
        Route::put('/{uuid}', [AdminController::class, 'updateAdmin'])->name('updateAdmin');
    });

    //MENTOR
    Route::prefix('mentor')->group(function () {
        Route::get('/', [MentorController::class, 'index'])->name('index');
        Route::get('/{id}', [MentorController::class, 'show'])->name('show');
        Route::put('/update/{id}', [MentorController::class, 'update'])->name('update');
        Route::delete('/{id}', [MentorController::class, 'destroy'])->name('destroy');
        Route::post('/create', [MentorController::class, 'store'])->name('store');
        Route::get('export/xlsx', [MentorController::class, 'exportMentorsToXLSX'])->name('exportxlsx');
        Route::get('export/csv', [MentorController::class, 'exportMentorsToCSV'])->name('exportCSV');
        Route::get('export/pdf', [MentorController::class, 'exportMentorsToPDF'])->name('exportPDF');
    });

    // COORDINATOR
    Route::prefix('coordinators')->group(function () {
        Route::get('/', [KoordinatorController::class, 'index'])->name('getallcoordinator');
        Route::get('/{user:uuid}', [KoordinatorController::class, 'show'])->name('getcoordinator');
        Route::post('/create', [KoordinatorController::class, 'store'])->name('createcoordinator');
        Route::patch('/update/{id}', [KoordinatorController::class, 'update'])->name('updatecoordinator');
        Route::delete('/{id}', [KoordinatorController::class, 'destroy'])->name('deletecoordinator');
        Route::post('/import', [KoordinatorController::class, 'importKoordinator'])->name('importcoordinators');
        Route::get('/export/xlsx', [KoordinatorController::class, 'exportKoordinatorToXLSX'])->name('exportcoordinatorstoxlsx');
        Route::get('/export/csv', [KoordinatorController::class, 'exportKoordinatorToCSV'])->name('exportcoordinatorstocsv');
        Route::get('/export/pdf', [KoordinatorController::class, 'exportKoordinatorToPDF'])->name('exportcoordinatorstopdf');
        Route::post('/major', [KoordinatorController::class, 'getCoordinatorsByMajors']); // for internship management needs
    });

    // STUDENT
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('getallstudenet');
        Route::get('/{user:uuid}', [StudentController::class, 'show'])->name('getstudenet');
        Route::post('/create', [StudentController::class, 'store'])->name('createstudenet');
        Route::patch('/update/{id}', [StudentController::class, 'update'])->name('updatestudenet');
        Route::delete('/{id}', [StudentController::class, 'destroy'])->name('deletestudenet');
        Route::post('/import', [StudentController::class, 'importStudent'])->name('importstudenets');
        Route::get('/export/xlsx', [StudentController::class, 'exportStudentToXLSX'])->name('exportstudenetstoxlsx');
        Route::get('/export/csv', [StudentController::class, 'exportStudentToCSV'])->name('exportstudenetstocsv');
        Route::get('/export/pdf', [StudentController::class, 'exportStudentToPDF'])->name('exportstudenetstopdf');
    });

    // OPPORTUNITY
    Route::prefix('opportunities')->group(function () {
        Route::get('/', [OpportunityController::class, 'index'])->name('index');
        Route::get('/{uuid}', [OpportunityController::class, 'show'])->name('show');
        Route::post('/update/{uuid}', [OpportunityController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [OpportunityController::class, 'destroy'])->name('destroy');
        Route::post('/create', [OpportunityController::class, 'store'])->name('store');
    });

    // PARTNER
    Route::prefix('partners')->group(function () {
        Route::get('/', [PartnerController::class, 'index'])->name('index');
        Route::get('/{uuid}', [PartnerController::class, 'show'])->name('show');
        Route::post('/update/{uuid}', [PartnerController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [PartnerController::class, 'destroy'])->name('destroy');
        Route::post('/create', [PartnerController::class, 'store'])->name('store');
    });

    Route::prefix('program-activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('index');
        Route::post('/create', [ActivityController::class, 'store'])->name('store');
    });

    // INTERNSHIP PROGRAM
    Route::prefix('internships')->group(function () {
        Route::get('/', [InternshipController::class, 'index'])->name('getallinternships');
        Route::get('/{internship:uuid}', [InternshipController::class, 'show'])->name('getinternship');
        Route::post('/create', [InternshipController::class, 'store'])->name('createinternship');
        Route::patch('/update/{internship:uuid}', [InternshipController::class, 'update'])->name('updateinternship');
        Route::delete('/{internship:uuid}', [InternshipController::class, 'destroy'])->name('deleteinternship');
    });

    // CLASS
    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassControllers::class, 'index'])->name('index');
        Route::get('/{uuid}', [ClassControllers::class, 'show'])->name('show');
        Route::post('/create', [ClassControllers::class, 'store'])->name('store');
        Route::put('/update/{uuid}', [ClassControllers::class, 'update'])->name('update');
        Route::delete('/{uuid}', [ClassControllers::class, 'destroy'])->name('destroy');
        Route::post('/search', [ClassControllers::class, 'search'])->name('search');
        Route::post('/major', [ClassControllers::class, 'getClassesByMajors']); // for internship management needs
    });
});
