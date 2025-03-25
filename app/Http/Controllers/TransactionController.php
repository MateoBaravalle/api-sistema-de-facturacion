<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest\StoreTransactionRequest;
use App\Http\Requests\TransactionRequest\UpdateTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    protected function getAllowedFilters(): array
    {
        return [
            'reference_type',
            'status',
            'due_date',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getAllTransactions(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Transacciones recuperadas', $transactions);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myIndex(Request $request): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getMyTransactions(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Transacciones recuperadas', $transactions);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);

            return $this->successResponse('Transacción recuperada', $transaction);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function myShow(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getMyTransactionById($id);

            return $this->successResponse('Transacción recuperada', $transaction);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());

            return $this->successResponse('Transacción creada', $transaction, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->updateTransaction($id, $request->validated());

            return $this->successResponse('Transacción actualizada', $transaction);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->transactionService->deleteTransaction($id);

            return $this->successResponse('Transacción eliminada');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showAverage(int $clientId): JsonResponse
    {
        try {
            $average = $this->transactionService->getAverageTransactionAmount($clientId);

            return $this->successResponse('Promedio de transacción recuperado', $average);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
