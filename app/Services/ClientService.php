<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService extends Service
{
    protected const MODEL = 'client';

    public function __construct(Client $client)
    {
        parent::__construct($client, self::MODEL);
    }

    public function getAllClients(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->remember(
            $this->getCacheKey('all', $page),
            fn () => $this->paginate($this->model->query(), $page, $perPage)
        );
    }

    public function getClientById(int $id): Client
    {
        return $this->getById($id);
    }

    public function createClient(array $data): Client
    {
        $client = $this->create($data);
        $this->clearModelCache($client->id, [self::MODEL]);
        return $client->fresh();
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
}
