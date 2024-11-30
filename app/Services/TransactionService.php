<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService extends Service
{
    protected const MODEL = 'transaction';

    public function __construct(Transaction $transaction)
    {
        parent::__construct($transaction, self::MODEL);
    }

    public function getAllTransactions(int $perPage): LengthAwarePaginator
    {
        return $this->getAll($perPage);
    }

    public function getTransactionById(int $id): Transaction
    {
        return $this->getById($id, self::MODEL);
    }

    public function getTransactionByClientId(int $clientId): Collection
    {
        return $this->remember(
            $this->getCacheKey('client', $clientId),
            fn () => $this->model->where('client_id', $clientId)
        );
    }

    public function createTransaction(array $data): Transaction
    {
        $transaction = $this->create($data);
        $this->clearModelCache($transaction->id, ['transaction']);
        return $transaction;
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = $this->getTransactionById($id);
        $transaction->update($data);
        
        $this->clearModelCache($id, ['transaction']);
        
        return $transaction->fresh();
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = $this->getTransactionById($id);
        $this->clearModelCache($id, ['transaction']);
        return $transaction->delete();
    }

    public function getTransactionsByStatus(string $status): Collection
    {
        return $this->remember(
            $this->getCacheKey('status', $status),
            fn () => $this->model->where('status', $status)
                ->orderBy('due_date', 'asc')
                ->get()
        );
    }

    public function getAverageTransactionAmount(int $clientId): array
    {
        $cacheKey = $this->getCacheKey('average', $clientId);
        $transactions = $this->getTransactionByClientId($clientId);

        $result = [
            'one_month_average' => $this->calculateAverageForPeriod($transactions, 1),
            'three_month_average' => $this->calculateAverageForPeriod($transactions, 3),
            'six_month_average' => $this->calculateAverageForPeriod($transactions, 6),
            'total_average' => $transactions->avg('amount') ?? 0,
        ];

        return $this->remember($cacheKey, fn () => $result);
    }

    private function calculateAverageForPeriod(Collection $transactions, int $months): float
    {
        return $transactions->where('created_at', '>=', now()->subMonths($months))
            ->avg('amount') ?? 0;
    }
}
