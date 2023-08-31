<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\Passwords\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\Passwords\ResetPasswordController;
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

Route::group(['prefix' => 'user'], function () {
    Route::post('login', LoginController::class);
    Route::post('forgot-password', ForgotPasswordController::class);
    Route::post('reset-password-token', ResetPasswordController::class);
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'is.admin'], function () {
        Route::post('logout', LogoutController::class);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::post('logout', LogoutController::class);
    });
});
