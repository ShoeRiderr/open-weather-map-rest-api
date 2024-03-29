<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OpenWeatherMapController;
use App\Http\Controllers\Api\UserController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    Route::group(['prefix' => 'weather'], function () {
        Route::get('current', [OpenWeatherMapController::class, 'getCurrentWeather']);
    });
});

