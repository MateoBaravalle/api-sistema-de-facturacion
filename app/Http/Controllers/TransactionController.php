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

    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $transactions = $this->transactionService->getAllTransactions($page, $perPage);
            return $this->successResponse('Transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(int $clientId): JsonResponse
    {
        try {
            $transactions = $this->transactionService->getTransactionByClient($clientId);
            return $this->successResponse('Transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');

            if (!$status) {
                throw new \InvalidArgumentException('Status is required');
            }

            $transactions = $this->transactionService->getTransactionsByStatus($status);
            return $this->successResponse("{$status} transactions retrieved successfully", ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyTransactions(): JsonResponse
    {
        return $this->getByClient(auth()->user()->client->id);
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

    public function showMyTransaction(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getMyTransactionById($id);
            return $this->successResponse('Transaction retrieved successfully', ['transaction' => $transaction]);
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

    public function showAverage(int $clientId): JsonResponse
    {
        try {
            $average = $this->transactionService->getAverageTransactionAmount($clientId);
            return $this->successResponse('Average transaction amount retrieved successfully', ['average' => $average]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
