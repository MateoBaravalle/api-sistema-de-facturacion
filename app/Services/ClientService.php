<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
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

    public function getTransactionHistory(int $clientId): Collection
    {
        $client = Client::findOrFail($clientId);
        return $client->transactions()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOverdueTransactions(int $clientId): Collection
    {
        $client = Client::findOrFail($clientId);
        return $client->transactions()
            ->where('status', 'overdue')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function calculatePurchaseAverages(int $clientId): array
    {
        $client = Client::findOrFail($clientId);
        $transactions = $client->transactions;

        return [
            'one_month_average' => $transactions->where('created_at', '>=', now()->subMonth())->avg('amount') ?? 0,
            'three_month_average' => $transactions->where('created_at', '>=', now()->subMonths(3))->avg('amount') ?? 0,
            'six_month_average' => $transactions->where('created_at', '>=', now()->subMonths(6))->avg('amount') ?? 0,
        ];
    }

    public function getClientOrders(int $clientId): Collection
    {
        $client = Client::findOrFail($clientId);
        return $client->orders()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getClientInvoices(int $clientId): Collection
    {
        $client = Client::findOrFail($clientId);
        return $client->invoices()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
