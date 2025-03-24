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

    public function getAllTransactions(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->model->query(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getMyTransactions(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->getMyThing(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getTransactionById(int $id): Transaction
    {
        return $this->getById($id);
    }

    public function getMyTransactionById(int $id): Transaction
    {
        if (!$this->belongsMe($id)) {
            throw new AuthorizationException('La transacción no pertenece al cliente actual');
        }

        return $this->getTransactionById($id);
    }

    public function createTransaction(array $data): Transaction
    {
        // $this->clearModelCache($transaction->id, ['transaction']);
        // $this->clearModelCacheWithSuffixes($transaction->client_id, ['average.client'], []);
        
        return $this->create($data);
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        $transaction = $this->update($id, $data);
        // $this->clearModelCache($id, ['transaction']);
        // $this->clearModelCacheWithSuffixes($transaction->client_id, ['average.client'], []);
        
        return $transaction->fresh();
    }

    public function updateMyTransaction(int $id, array $data): Transaction
    {
        if (!$this->belongsMe($id)) {
            throw new AuthorizationException('La transacción no pertenece al cliente actual');
        }

        return $this->updateTransaction($id, $data);
    }

    public function deleteTransaction(int $id): bool
    {
        // $this->clearModelCache($id, ['transaction']);
        // $this->clearModelCacheWithSuffixes($transaction->client_id, ['average.client'], []);

        return $this->delete($id);
    }

    public function getAverageTransactionAmount(int $clientId): array
    {
        $transactions = $this->getTransactionByClient($clientId);
        $result = [
            'one_month_average' => $this->calculateAverageForPeriod($transactions, 1),
            'three_month_average' => $this->calculateAverageForPeriod($transactions, 3),
            'six_month_average' => $this->calculateAverageForPeriod($transactions, 6),
            'total_average' => $transactions->avg('amount') ?? 0,
        ];

        // $cacheKey = $this->getCacheKey('average.client', $clientId);
        // return $this->remember($cacheKey, fn () => $result);
        
        return $result;
    }

    private function calculateAverageForPeriod(Collection $transactions, int $months): float
    {
        return $transactions->where('created_at', '>=', now()->subMonths($months))
            ->avg('amount') ?? 0;
    }

    private function getTransactionByClient(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->get();
    }
}
