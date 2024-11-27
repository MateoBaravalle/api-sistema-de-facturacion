<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
        Route::get('/role', [RoleController::class, 'index']);
        Route::post('/role', [RoleController::class, 'store']);
        Route::post('/role/{id}/assign', [RoleController::class, 'assignUser']);
        
        // Admin routes
        Route::get('/user', [UserController::class, 'index']);
        Route::post('/user', [UserController::class, 'store']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
        Route::group(['middleware' => 'role:admin'], function () {
        });

        // Supervisor routes
        Route::get('/client', [ClientController::class, 'index']);
        Route::get('/client/{id}', [ClientController::class, 'show']);
        Route::post('/client', [ClientController::class, 'store']);
        Route::put('/client/{id}', [ClientController::class, 'update']);
        Route::delete('/client/{id}', [ClientController::class, 'destroy']);
        Route::get('/client/{id}/transactions', [ClientController::class, 'transactions']);// Cambiar, usar ruta y modelo de transacciones
        Route::get('/client/{id}/purchase-average', [ClientController::class, 'purchaseAverage']);// Integrar una prop en el modelo de cliente para mostrar el promedio de compras - Que el mismo se calcule x cron job
        Route::get('/client/{id}/orders', [ClientController::class, 'orders']);// Cambiar, usar ruta y modelo de ordenes
        Route::get('/client/{id}/invoices', [ClientController::class, 'invoices']);// Cambiar, usar ruta y modelo de facturas
        Route::group(['middleware' => 'role:supervisor'], function () {
        });

        // Client routes
        Route::get('/client/me', [ClientController::class, 'showProfile']);
        Route::put('/client/me', [ClientController::class, 'updateProfile']);
        Route::get('/client/purchase-average', [ClientController::class, 'showPurchaseAverage']);// Integrar una prop en el modelo de cliente para mostrar el promedio de compras - Que el mismo se calcule x cron job
        Route::get('/client/transactions', [ClientController::class, 'showTransactions']); // Cambiar, usar ruta y modelo de transacciones
        Route::get('/client/orders', [ClientController::class, 'showOrders']);// Cambiar, usar ruta y modelo de ordenes
        Route::get('/client/invoices', [ClientController::class, 'showInvoices']);// Cambiar, usar ruta y modelo de facturas
        Route::put('/user/me', [UserController::class, 'updateProfile']);
        Route::group(['middleware' => 'role:client'], function () {
        });
        
        // Guest routes
        Route::get('/user/me', [UserController::class, 'showProfile']);// Vista del perfil antes de obtener un rol
        Route::group(['middleware' => 'role:guest'], function () {
        });

        // Rutas de Provider
        // Route::get('/provider', [ProviderController::class, 'index']);
        // Route::post('/provider', [ProviderController::class, 'store']);
        // Route::get('/provider/{id}', [ProviderController::class, 'show']);
        // Route::put('/provider/{id}', [ProviderController::class, 'update']);
        // Route::delete('/provider/{id}', [ProviderController::class, 'destroy']);

    });
});
