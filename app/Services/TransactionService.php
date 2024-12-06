<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
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

    public function getTransactionByClient(int $clientId, int $perPage = null): Collection | LengthAwarePaginator
    {
        $cacheKey = $perPage
            ? $this->getCacheKey("client.{$clientId}.page.{$perPage}")
            : $this->getCacheKey("client.{$clientId}.all");

        return $this->remember(
            $cacheKey,
            function () use ($clientId, $perPage) {
                $query = $this->model->where('client_id', $clientId);

                if ($perPage) {
                    return $this->paginate($query, $perPage);
                }

                return $query->get();
            }
        );
    }

    public function getTransactionsByStatus(string $status, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey("status.{$status}");

        return $this->remember(
            $cacheKey,
            fn () => $this->paginate(
                $this->model->where('status', $status)
                    ->orderBy('due_date', 'asc'),
                $perPage
            )
        );
    }

    public function getTransactionById(int $id): Transaction
    {
        return $this->getById($id);
    }

    public function getMyTransactionById(int $id): Transaction
    {
        if (!$this->belongsToClient($id)) {
            throw new AuthorizationException('Transaction does not belong to the current client');
        }

        return $this->getTransactionById($id);
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

    public function updateMyTransaction(int $id, array $data): Transaction
    {
        if (!$this->belongsToClient($id)) {
            throw new AuthorizationException('Transaction does not belong to the current client');
        }

        return $this->updateTransaction($id, $data);
    }

    public function deleteTransaction(int $id): bool
    {
        $transaction = $this->getTransactionById($id);
        $this->clearModelCache($id, ['transaction']);
        return $transaction->delete();
    }

    public function getAverageTransactionAmount(int $clientId): array
    {
        $cacheKey = $this->getCacheKey('average.client', $clientId);
        $transactions = $this->getTransactionByClient($clientId);

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
