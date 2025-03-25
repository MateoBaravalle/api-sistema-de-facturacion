<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
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
            Route::post('/client/me', [ClientController::class, 'storeProfile']); // Crear un cliente para el usuario
            Route::put('/client/me', [ClientController::class, 'updateProfile']); // Actualizar el perfil del cliente
            
            Route::post('/order/me', [OrderController::class, 'myStore']); // Generar un pedido
            Route::put('/order/me/{id}', [OrderController::class, 'myUpdate']); // Actualizar un pedido (1hs despues de creado)
            
            Route::get('/order/me', [OrderController::class, 'myIndex']); // Seguimiento de mis pedidos
            Route::get('/order/me/{id}', [OrderController::class, 'myShow']); // Vista de un pedido, con factura y productos
            
            Route::get('/transactions/me', [TransactionController::class, 'myIndex']); // Vista de las transacciones del cliente
            Route::get('/transactions/me/{id}', [TransactionController::class, 'myShow']); // Vista de una transacción
            
            Route::get('/payments/me', [PaymentController::class, 'myIndex']); // Vista de los pagos del cliente
            Route::post('payments/me', [PaymentController::class, 'store']); // Crear un pago

            Route::get('/invoice/me', [InvoiceController::class, 'myIndex']); // Vista de las facturas del cliente
            Route::get('/invoice/me/{id}', [InvoiceController::class, 'myShow']); // Vista de una factura
            
            Route::get('notifications', [NotificationController::class, 'index']); // Get notificaciones paginadas
            
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
            Route::get('/client/{id}/orders', [OrderController::class, 'index']); // Get ordenes de un cliente con productos y facturas
            Route::get('/client/{id}/transactions', [TransactionController::class, 'index']); // Get transacciones de un cliente
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
            Route::get('/transaction/status', [TransactionController::class, 'index']); // Get transacciones por estado

            // CRUD de Facturas
            Route::get('/invoice', [InvoiceController::class, 'index']); // Get facturas paginadas
            Route::get('/invoice/{id}', [InvoiceController::class, 'show']); // Vista de una factura
            Route::post('/invoice', [InvoiceController::class, 'store']); // Crear una factura
            Route::get('/invoice/status', [InvoiceController::class, 'index']); // Get facturas por estado

            // CRUD de Productos
            Route::get('products', [ProductController::class, 'index']); // Get productos paginados
            Route::post('products', [ProductController::class, 'store']); // Crear un producto
            Route::put('products/{id}', [ProductController::class, 'update']); // Actualizar un producto
            Route::delete('products/{id}', [ProductController::class, 'destroy']); // Eliminar un producto

            // CRUD de Listas de Precios
            Route::get('price-list/{id}', [ProductController::class, 'index']); // Vista de una lista de precios
            Route::post('price-list', [ProductController::class, 'store']); // Crear una lista de precios
            Route::put('price-list/{id}', [ProductController::class, 'update']); // Actualizar una lista de precios
            Route::delete('price-list/{id}', [ProductController::class, 'destroy']);
            
            // CRUD de Descuentos
            // Route::get('discounts', [DiscountController::class, 'index']); // Get descuentos paginados
            // Route::post('discounts', [DiscountController::class, 'store']); // Crear un descuento
            // Route::put('discounts/{id}', [DiscountController::class, 'update']); // Actualizar un descuento
            // Route::delete('discounts/{id}', [DiscountController::class, 'destroy']); // Eliminar un descuento

            // CRUD de Notificaciones
            Route::post('notifications', [NotificationController::class, 'store']); // Crear una notificación
            Route::put('notifications/{id}', [NotificationController::class, 'update']); // Actualizar una notificación
            Route::delete('notifications/{id}', [NotificationController::class, 'destroy']); // Eliminar una notificación

            // CRUD de Pagos
            Route::get('payments', [PaymentController::class, 'index']); // Get pagos paginados
            Route::post('payments', [PaymentController::class, 'store']); // Crear un pago
            Route::put('payments/{id}', [PaymentController::class, 'update']); // Actualizar un pago
            Route::delete('payments/{id}', [PaymentController::class, 'destroy']); // Eliminar un pago
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
