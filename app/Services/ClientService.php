<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService
{
    public function getAllClients(int $perPage = 10): LengthAwarePaginator
    {
        return Client::paginate($perPage);
    }

    public function getClientById(int $id): Client
    {
        return Client::findOrFail($id);
    }

    public function createClient(array $data): Client
    {
        return Client::create($data);
    }

    public function updateClient(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);
        $client->update($data);
        return $client;
    }

    public function deleteClient(int $id): void
    { 
        $client = Client::findOrFail($id);
        $client->delete();
    }
}
