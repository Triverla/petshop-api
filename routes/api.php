<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
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

Route::post('admin/login', LoginController::class);
Route::post('user/login', LoginController::class);

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'is.admin'], function () {
        Route::post('logout', LogoutController::class);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::post('logout', LogoutController::class);
    });
});
