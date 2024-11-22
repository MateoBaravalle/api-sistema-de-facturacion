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
    // Login and Register
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Authenticated routes
    Route::group(['middleware' => 'auth:api'], function () {
        // Logout and Refresh
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/refresh', [AuthController::class, 'refresh']);
        
        // Admin routes
        Route::group(['middleware' => 'role:admin'], function () {
            Route::get('/user', [AuthController::class, 'index']);
            Route::post('/user', [AuthController::class, 'store']);
            Route::put('/user/{id}', [AuthController::class, 'update']);
            Route::delete('/user/{id}', [AuthController::class, 'destroy']);
        });

        // Supervisor routes
        Route::group(['middleware' => 'role:supervisor'], function () {
            Route::get('/client', [ClientController::class, 'index']);
            Route::get('/client/{id}', [ClientController::class, 'show']);
            Route::post('/client', [ClientController::class, 'store']);
            Route::put('/client/{id}', [ClientController::class, 'update']);
            Route::delete('/client/{id}', [ClientController::class, 'destroy']);
            Route::get('/client/{id}/transactions', [ClientController::class, 'transactions']);
            Route::get('/client/{id}/purchase-average', [ClientController::class, 'purchaseAverage']);
            Route::get('/client/{id}/orders', [ClientController::class, 'orders']);
            Route::get('/client/{id}/invoices', [ClientController::class, 'invoices']);
        });

        // Client routes
        Route::group(['middleware' => 'role:client'], function () {
            Route::get('/client/me', [ClientController::class, 'showProfile']);
            Route::put('/client/me', [ClientController::class, 'updateProfile']);
        });

        // Guest routes
        Route::group(['middleware' => 'role:guest'], function () {
            Route::get('/user/{id}', [AuthController::class, 'show']);
        });

        // Rutas de Provider
        // Route::get('/provider', [ProviderController::class, 'index']);
        // Route::post('/provider', [ProviderController::class, 'store']);
        // Route::get('/provider/{id}', [ProviderController::class, 'show']);
        // Route::put('/provider/{id}', [ProviderController::class, 'update']);
        // Route::delete('/provider/{id}', [ProviderController::class, 'destroy']);

    });
});
