<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest\StoreOrderRequest;
use App\Http\Requests\OrderRequest\UpdateOrderRequest;
use App\Services\OrderService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $orders = $this->orderService->getAllOrders($page, $perPage);
            return $this->successResponse('Ordenes recuperadas', ['orders' => $orders]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(Request $request = null, int $id = null): JsonResponse
    {
        try {
            $perPage = $request->get('per_page') ?? 25;
            $orders = $this->orderService->getOrdersByClient($id, $perPage);
            return $this->successResponse('Ordenes recuperadas', ['orders' => $orders]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyOrders(): JsonResponse
    {
        $clientId = auth()->user()->client->id;

        if (!$clientId) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        return $this->getByClient($clientId);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);
            return $this->successResponse('Orden recuperada', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showMyOrder(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getMyOrderById($id);
            return $this->successResponse('Orden recuperada', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return $this->successResponse('Orden creada', ['order' => $order], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function storeMyOrder(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createMyOrder($request->validated());
            return $this->successResponse('Orden creada', ['order' => $order], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateOrder($id, $request->validated());
            return $this->successResponse('Orden actualizada', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateMyOrder(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateMyOrder($id, $request->validated());
            return $this->successResponse('Orden actualizada', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return $this->successResponse('Orden eliminada');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
