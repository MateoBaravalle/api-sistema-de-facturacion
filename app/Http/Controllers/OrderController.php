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

    protected function getAllowedFilters(): array
    {
        return [
            'reference_type',
            'order_date',
            'status',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->getAllOrders(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Ordenes recuperadas', $orders);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myIndex(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->getMyOrders(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Ordenes recuperadas', $orders);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);

            return $this->successResponse('Orden recuperada', $order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myShow(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getMyOrderById($id);

            return $this->successResponse('Orden recuperada', $order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->validated()
            );

            return $this->successResponse('Orden creada', $order, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myStore(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createMyOrder(
                $request->validated()
            );

            return $this->successResponse('Orden creada', $order, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateOrder(
                $id,
                $request->validated()
            );

            return $this->successResponse('Orden actualizada', $order);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myUpdate(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->updateMyOrder(
                $id,
                $request->validated()
            );

            return $this->successResponse('Orden actualizada', $order);
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

    public function showAverage(int $clientId): JsonResponse
    {
        try {
            $average = $this->orderService->getAverageOrderAmount($clientId);

            return $this->successResponse('Promedio de ordenes recuperado', $average);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
