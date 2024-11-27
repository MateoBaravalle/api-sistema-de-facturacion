<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService extends Service
{
    public function __construct(Client $client)
    {
        parent::__construct($client, 'client');
    }

    public function getAllClients(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->remember('clients.all', fn () =>$this->paginate($this->model->query(), $perPage));
    }

    public function getClientById(int $id): Client
    {
        return $this->model->findOrFail($id);
    }

    public function createClient(array $data): Client
    {
        return $this->model->create($data);
    }

    public function updateClient(int $id, array $data): Client
    {
        $client = $this->getClientById($id);
        $client->update($data);
        
        $this->clearModelCacheWithSuffixes(
            $id,
            ['client', 'transactions', 'orders', 'invoices'],
            ['pending', 'completed', 'averages']
        );
        
        return $client->fresh();
    }

    public function deleteClient(int $id): bool
    {
        return $this->getClientById($id)->delete();
    }

    public function getTransactionHistory(int $clientId): Collection
    {
        return $this->remember(
            $this->getCacheKey('transactions', $clientId),
            fn () => $this->getClientById($clientId)->transactions
        );
    }

    public function getOverdueTransactions(int $clientId): Collection
    {
        $query = $this->buildOverdueTransactionsQuery($clientId);
        return $query->get();
    }

    public function calculatePurchaseAverages(int $clientId): array
    {
        $cacheKey = $this->getCacheKey('averages', $clientId);
        return $this->remember($cacheKey, fn () => $this->computeAverages($clientId));
    }

    public function getClientOrders(int $clientId): Collection
    {
        $cacheKey = $this->getCacheKey('orders', $clientId);
        return $this->remember($cacheKey, fn () => $this->fetchOrders($clientId));
    }

    public function getClientInvoices(int $clientId): Collection
    {
        $cacheKey = $this->getCacheKey('invoices', $clientId);
        return $this->remember($cacheKey, fn () => $this->fetchInvoices($clientId));
    }

    private function calculateAverageForPeriod(Collection $transactions, int $months): float
    {
        return $transactions->where('created_at', '>=', now()->subMonths($months))
            ->avg('amount') ?? 0;
    }

    private function buildOverdueTransactionsQuery(int $clientId): Builder
    {
        return $this->getTransactionHistory($clientId)
            ->where('status', 'overdue')
            ->pipe(fn ($query) => $this->getOrderedQuery($query, 'due_date', 'asc'));
    }

    private function computeAverages(int $clientId): array
    {
        $transactions = $this->getTransactionHistory($clientId);
        
        return [
            'one_month_average' => $this->calculateAverageForPeriod($transactions, 1),
            'three_month_average' => $this->calculateAverageForPeriod($transactions, 3),
            'six_month_average' => $this->calculateAverageForPeriod($transactions, 6),
            'total_average' => $transactions->avg('amount') ?? 0,
        ];
    }

    private function fetchOrders(int $clientId): Collection
    {
        $query = $this->getClientById($clientId)->orders()->query();
        return $this->getOrderedQuery($query)->get();
    }

    private function fetchInvoices(int $clientId): Collection
    {
        $query = $this->getClientById($clientId)->invoices()->query();
        return $this->getOrderedQuery($query)->get();
    }
}
