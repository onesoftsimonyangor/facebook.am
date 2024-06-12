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
    Route::resource('/user', UserController::class)->only('store', 'show', 'destroy');
    Route::put('/user-update', [UserController::class, 'updateUser']);
    Route::put('/change-password', [RegisterController::class, 'changePassword']);
    Route::post('/user-logout', [RegisterController::class, 'logout']);
    Route::post('/upload-mixed-media', [ImageUploadController::class, 'uploadMixedMedia']);
});
