<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest\StoreTransactionRequest;
use App\Http\Requests\TransactionRequest\UpdateTransactionRequest;
use App\Services\TransactionService;
use Illuminate\Auth\Access\AuthorizationException;
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
            return $this->successResponse('Transacciones recuperadas', ['transacciones' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(int $clientId, Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $transactions = $this->transactionService->getTransactionByClient($clientId, $page, $perPage);
            return $this->successResponse('Transacciones recuperadas', ['transacciones' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);

            if (!$status) {
                throw new \InvalidArgumentException('El status es requerido');
            }

            $transactions = $this->transactionService->getTransactionsByStatus($status, $page, $perPage);
            return $this->successResponse("{$status} transacciones recuperadas", ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyTransactions(Request $request): JsonResponse
    {
        $clientId = auth()->user()->client->id;
     
        if (!$clientId) {
            throw new AuthorizationException('Cliente no encontrado');
        }
     
        return $this->getByClient($clientId, $request);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionById($id);
            return $this->successResponse('Transacción recuperada', ['transaction' => $transaction]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showMyTransaction(int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getMyTransactionById($id);
            return $this->successResponse('Transacción recuperada', ['transaction' => $transaction]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction($request->validated());
            return $this->successResponse('Transacción creada', ['transaction' => $transaction], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        try {
            $transaction = $this->transactionService->updateTransaction($id, $request->validated());
            return $this->successResponse('Transacción actualizada', ['transaction' => $transaction]);
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
            return $this->successResponse('Promedio de transacción recuperado', ['average' => $average]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
