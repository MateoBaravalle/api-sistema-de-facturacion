<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService extends Service
{
    protected const MODEL = 'client';

    public function __construct(Client $client)
    {
        parent::__construct($client, self::MODEL);
    }

    public function getAllClients(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->remember(
            $this->getCacheKey('all'),
            fn () => $this->paginate($this->model->query(), $perPage)
        );
    }

    public function getClientById(int $id): Client
    {
        return $this->getById($id, self::MODEL);
    }

    public function createClient(array $data): Client
    {
        $client = $this->create($data);
        $this->clearModelCache($client->id, [self::MODEL]);
        return $client;
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
        $deleted = $this->getClientById($id)->delete();

        if ($deleted) {
            $this->clearModelCacheWithSuffixes(
                $id,
                ['client', 'transactions', 'orders', 'invoices'],
                ['pending', 'completed', 'averages']
            );
        }

        return $deleted;
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
