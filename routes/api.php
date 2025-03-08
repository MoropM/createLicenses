<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\LicenseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('genNewLicence/{days?}/{uri?}', [LicenseController::class, 'generateLicenseNew']);


Route::post('license_create', [LicenseController::class, 'create']);


// Route::post('register', [UserController::class, 'register']);
// Route::post('login', [UserController::class, 'login']);

Route::group( ['middleware' => ["auth:sanctum"]], function(){

    // Route::get('user-profile', [UserController::class, 'userProfile']);
    // Route::get('logout', [UserController::class, 'logout']);


    // Route::get('verify_license', [LicenseController::class, 'verifyLicense']);
    
});
Route::get('licences', [LicenseController::class, 'index']);
Route::get('verify_license', [LicenseController::class, 'verifyLicense']);
Route::post('verify_license', [LicenseController::class, 'verifyLicense']);