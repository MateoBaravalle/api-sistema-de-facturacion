<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest\StoreTransactionRequest;
use App\Http\Requests\TransactionRequest\UpdateTransactionRequest;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $transactions = $this->transactionService->getAllTransactions($perPage);
            return $this->successResponse('Transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());
            return $this->successResponse('Transaction created successfully', ['transaction' => $transaction], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            return $this->successResponse('Transaction retrieved successfully', ['transaction' => $transaction]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->updateTransaction($id, $request->validated());
            return $this->successResponse('Transaction updated successfully', ['transaction' => $transaction]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->transactionService->deleteTransaction($id);
            return $this->successResponse('Transaction deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function pending(): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByStatus('pending');
            return $this->successResponse('Pending transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function overdue(): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionsByStatus('overdue');
            return $this->successResponse('Overdue transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
