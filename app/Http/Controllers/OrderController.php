<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest\StoreOrderRequest;
use App\Http\Requests\OrderRequest\UpdateOrderRequest;
use App\Services\OrderService;
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
            $perPage = $request->get('per_page', 10);
            $orders = $this->orderService->getAllOrders($perPage);
            return $this->successResponse('Orders retrieved successfully', [...$orders]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(Request $request = null, int $id = null): JsonResponse
    {
        try {
            $perPage = $request->get('per_page') ?? 25;
            $client = $this->orderService->getOrdersByClient($id, $perPage);
            return $this->successResponse('Orders retrieved successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyOrders(): JsonResponse
    {
        return $this->getByClient(auth()->user()->client->id);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->orderService->getOrderById($id);
            return $this->successResponse('Order retrieved successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showMyOrder(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getMyOrderById($id);
            return $this->successResponse('Order retrieved successfully', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $client = $this->orderService->createOrder($request->validated());
            return $this->successResponse('Order created successfully', ['client' => $client], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function storeMyOrder(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createMyOrder($request->validated());
            return $this->successResponse('Order created successfully', ['order' => $order], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->orderService->updateOrder($id, $request->validated());
            return $this->successResponse('Order updated successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateMyOrder(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateMyOrder($id, $request->validated());
            return $this->successResponse('Order updated successfully', ['order' => $order]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return $this->successResponse('Order deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
