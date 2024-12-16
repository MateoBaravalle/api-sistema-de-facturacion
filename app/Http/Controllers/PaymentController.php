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

    public function getAll(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $payments = $this->paymentService->getAllPayments($page, $perPage);
            return $this->successResponse('Pagos recuperados', ['pagos' => $payments]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->createPayment($request->validated());
            return $this->successResponse('Pago creado', ['pago' => $payment], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdatePaymentRequest $request, int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->updatePayment($id, $request->validated());
            return $this->successResponse('Pago actualizado', ['pago' => $payment]);
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
