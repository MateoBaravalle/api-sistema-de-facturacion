<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'api'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/refresh', [AuthController::class, 'refresh']);

        // Rutas de Client
        Route::get('/client', [ClientController::class, 'index']);
        Route::post('/client', [ClientController::class, 'store']);
        Route::get('/client/{id}', [ClientController::class, 'show']);
        Route::put('/client/{id}', [ClientController::class, 'update']);
        Route::delete('/client/{id}', [ClientController::class, 'destroy']);
    });
});
