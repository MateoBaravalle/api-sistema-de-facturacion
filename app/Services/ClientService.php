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
        return $this->getAll($page, $perPage);
    }

    public function getClientById(int $id): Client
    {
        return $this->getById($id);
    }

    public function createClient(array $data): Client
    {
        // $this->clearModelCache($client->id, [self::MODEL]);
        
        return $this->create($data);
    }

    public function updateClient(int $id, array $data): Client
    {
        // $this->clearModelCacheWithSuffixes(
        //     $id,
        //     ['client', 'transactions', 'orders', 'invoices'],
        //     ['pending', 'completed', 'averages']
        // );

        return $this->update($id, $data);
    }

    public function deleteClient(int $id): bool
    {
        // if ($deleted) {
        //     $this->clearModelCacheWithSuffixes(
        //         $id,
        //         ['client', 'transactions', 'orders', 'invoices'],
        //         ['pending', 'completed', 'averages']
        //     );
        // }

        return $this->delete($id);
    }
}
