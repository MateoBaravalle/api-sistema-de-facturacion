<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
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
    // Registro y login
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rutas autenticadas
    Route::group(['middleware' => 'auth:api'], function () {
        // Manejo de autenticación
        Route::get('/logout', [AuthController::class, 'logout']); // Cerrar sesión
        Route::get('/refresh', [AuthController::class, 'refresh']); // Refrescar token

        // Guest routes
        Route::group(['middleware' => 'role:guest'], function () {
            Route::get('/user/me', [UserController::class, 'showProfile']);// Vista del perfil del usuario
        });

        // Client routes ✅
        Route::group(['middleware' => 'role:client'], function () {

            Route::get('/client/me', [ClientController::class, 'showProfile']); // Vista del perfil del cliente
            Route::put('/client/me', [ClientController::class, 'updateProfile']); // Actualizar el perfil del cliente
            
            Route::post('/order/me', [OrderController::class, 'storeMyOrder']); // Generar un pedido
            Route::put('/order/me/{id}', [OrderController::class, 'updateMyOrder']); // Actualizar un pedido (1hs despues de creado)

            Route::get('/order/me', [OrderController::class, 'getMyOrders']); // Seguimiento de mis pedidos
            Route::get('/order/me/{id}', [OrderController::class, 'showMyOrder']); // Vista de un pedido, con factura y productos
            
            Route::get('/transactions/me', [TransactionController::class, 'getMyTransactions']); // Vista de las transacciones del cliente
            Route::get('/transactions/me/{id}', [TransactionController::class, 'showMyTransaction']); // Vista de una transacción

            Route::get('/invoice/me', [InvoiceController::class, 'getMyInvoices']); // Vista de las facturas del cliente
            // Route::get('/invoice/me/{id}', [InvoiceController::class, 'showMyInvoice']); // Vista de una factura

            Route::put('/user/me', [UserController::class, 'updateProfile']); // Actualizar el perfil del usuario
        });

        // Supervisor routes
        Route::group(['middleware' => 'role:supervisor'], function () {
            // CRUD de clientes
            Route::get('/client', [ClientController::class, 'index']); // Get clientes paginados
            Route::get('/client/{id}', [ClientController::class, 'show']); // Vista de un cliente
            Route::post('/client', [ClientController::class, 'store']); // Crear un cliente
            Route::put('/client/{id}', [ClientController::class, 'update']); // Actualizar un cliente
            Route::delete('/client/{id}', [ClientController::class, 'destroy']); // Eliminar un cliente

            // Vista de transacciones, promedio de compras y ordenes de un cliente
            Route::get('/client/{id}/orders', [OrderController::class, 'getByClient']); // Get ordenes de un cliente con productos y facturas
            Route::get('/client/{id}/transactions', [TransactionController::class, 'getByClient']); // Get transacciones de un cliente
            Route::get('/client/{id}/purchase-average', [TransactionController::class, 'showAverage']); // Get promedio de compras de un cliente

            // CRUD de Ordenes
            Route::get('/order', [OrderController::class, 'index']); // Get ordenes paginadas
            Route::get('/order/{id}', [OrderController::class, 'show']); // Vista de una orden
            Route::post('/order', [OrderController::class, 'store']); // Crear una orden
            Route::put('/order/{id}', [OrderController::class, 'update']); // Actualizar una orden
            Route::delete('/order/{id}', [OrderController::class, 'destroy']); // Eliminar una orden

            // CRUD de Transacciones
            Route::get('/transaction', [TransactionController::class, 'index']); // Get transacciones paginadas
            Route::get('/transaction/{id}', [TransactionController::class, 'show']); // Vista de una transacción
            Route::post('/transaction', [TransactionController::class, 'store']); // Crear una transacción
            Route::get('/transaction/status', [TransactionController::class, 'getByStatus']); // Get transacciones por estado

            // CRUD de Facturas
            Route::get('/invoice', [InvoiceController::class, 'index']); // Get facturas paginadas
            Route::get('/invoice/{id}', [InvoiceController::class, 'show']); // Vista de una factura
            Route::post('/invoice', [InvoiceController::class, 'store']); // Crear una factura
            Route::get('/invoice/status', [InvoiceController::class, 'getByStatus']); // Get facturas por estado

        });

        // Admin routes
        Route::group(['middleware' => 'role:admin'], function () {
            // Manejo de roles
            Route::get('/role', [RoleController::class, 'index']);
            Route::post('/role/{id}/assign', [RoleController::class, 'assignUser']);
            
            // Manejo de usuarios
            Route::get('/user', [UserController::class, 'index']); // Get usuarios paginados
            Route::post('/user', [UserController::class, 'store']); // Crear un usuario
            Route::put('/user/{id}', [UserController::class, 'update']); // Actualizar un usuario
            Route::delete('/user/{id}', [UserController::class, 'destroy']); // Eliminar un usuario
            
            // Manejo de transacciones
            Route::put('/transaction/{id}', [TransactionController::class, 'update']); // Actualizar una transacción
            Route::delete('/transaction/{id}', [TransactionController::class, 'destroy']); // Eliminar una transacción
            
            // Manejo de facturas
            Route::put('/invoice/{id}', [InvoiceController::class, 'update']); // Actualizar una factura
            Route::delete('/invoice/{id}', [InvoiceController::class, 'destroy']); // Eliminar una factura

            // Vista de métricas
            // Route::get('/metrics', [MetricController::class, 'index']); // Get métricas
        });
    });
});
