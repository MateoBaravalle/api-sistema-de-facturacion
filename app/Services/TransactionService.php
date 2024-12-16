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

    public function getAllTransactions(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }

    public function getTransactionByClient(int $clientId, int $page = 1, int $perPage = null): Collection | LengthAwarePaginator
    {
        // $cacheKey = $perPage
        //     ? $this->getCacheKey('client', $clientId . $page . $perPage)
        //     : $this->getCacheKey('client', $clientId . 'all');
        // return $this->remember(
        //     $cacheKey,
        //     function () use ($clientId, $page, $perPage) {
        //         $query = $this->model->where('client_id', $clientId);
        //         if ($perPage) {
        //             return $this->paginate($query, $page, $perPage);
        //         }
        //         return $query->get();
        //     }
        // );

        $query = $this->model->query()->where('client_id', $clientId);
        
        return $perPage
            ? $this->paginate($query, $page, $perPage)
            : $query->get();
    }

    public function getTransactionsByStatus(string $status, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        // $cacheKey = $this->getCacheKey('status', $status . $page . $perPage);
        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->paginate(
        //         $this->model->where('status', $status)
        //             ->orderBy('due_date', 'asc'),
        //         $page,
        //         $perPage
        //     )
        // );
        $query = $this->model->query()
            ->where('status', $status)
            ->orderBy('due_date', 'asc');
        
        return $this->paginate($query, $page, $perPage);
    }

    public function getTransactionById(int $id): Transaction
    {
        return $this->getById($id);
    }

    public function getMyTransactionById(int $id): Transaction
    {
        if (!$this->belongsToClient($id)) {
            throw new AuthorizationException('La transacción no pertenece al cliente actual');
        }

        return $this->getTransactionById($id);
    }

    public function createTransaction(array $data): Transaction
    {
        // $this->clearModelCache($transaction->id, ['transaction']);
        
        return $this->create($data);
    }

    public function updateTransaction(int $id, array $data): Transaction
    {
        // $this->clearModelCache($id, ['transaction']);
        
        return $this->update($id, $data);
    }

    public function updateMyTransaction(int $id, array $data): Transaction
    {
        if (!$this->belongsToClient($id)) {
            throw new AuthorizationException('La transacción no pertenece al cliente actual');
        }

        return $this->updateTransaction($id, $data);
    }

    public function deleteTransaction(int $id): bool
    {
        // $this->clearModelCache($id, ['transaction']);

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
}
