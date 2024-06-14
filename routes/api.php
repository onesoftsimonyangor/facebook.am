<?php

use App\Http\Controllers\API\ImageUploadController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user-show', [UserController::class, 'show']);
    Route::post('/upload-user-images', [UserController::class, 'store']);
    Route::post('/add-user-main-image/{userImage}', [UserController::class, 'addMainImage']);
    Route::put('/user-update', [UserController::class, 'updateUser']);
    Route::get('/user-images', [UserController::class, 'getUserImages']);
    Route::put('/change-password', [RegisterController::class, 'changePassword']);
    Route::post('/user-logout', [RegisterController::class, 'logout']);
    Route::delete('/user-image-delete/{userImage}', [UserController::class, 'deleteUserImage']);
    Route::delete('/user-delete', [UserController::class, 'destroy']);
    Route::post('/upload-mixed-media', [ImageUploadController::class, 'uploadMixedMedia']);
});
