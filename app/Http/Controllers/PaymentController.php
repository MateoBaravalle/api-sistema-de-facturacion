<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest\StorePaymentRequest;
use App\Http\Requests\PaymentRequest\UpdatePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    protected function getAllowedFilters(): array
    {
        return [
            'transaction_id',
            'payment_method',
            'status',
            'payment_date',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $payments = $this->paymentService->getAllPayments(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Pagos recuperados', $payments);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myIndex(Request $request): JsonResponse
    {
        try {
            $payments = $this->paymentService->getMyPayments(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Pagos recuperados', $payments);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->createPayment(
                $request->validated()
            );

            return $this->successResponse('Pago creado', $payment, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->updatePayment(
                $id,
                $request->validated()
            );

            return $this->successResponse('Pago actualizado', $payment);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->paymentService->deletePayment($id);

            return $this->successResponse('Pago eliminado');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
