<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest\StoreClientRequest;
use App\Http\Requests\ClientRequest\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $clients = $this->clientService->getAllClients($perPage);
            return $this->successResponse('Clients retrieved successfully', ['clients' => $clients]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->successResponse('Client created successfully', ['client' => $client], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            return $this->successResponse('Client retrieved successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());
            return $this->successResponse('Client updated successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clientService->deleteClient($id);
            return $this->successResponse('Client deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function transactions(int $id): JsonResponse
    {
        try {
            $transactions = $this->clientService->getTransactionHistory($id);
            return $this->successResponse('Transactions retrieved successfully', ['transactions' => $transactions]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function purchaseAverage(int $id): JsonResponse
    {
        try {
            $averages = $this->clientService->calculatePurchaseAverages($id);
            return $this->successResponse('Purchase averages calculated successfully', ['averages' => $averages]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function orders(int $id): JsonResponse
    {
        try {
            $orders = $this->clientService->getClientOrders($id);
            return $this->successResponse('Orders retrieved successfully', ['orders' => $orders]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function invoices(int $id): JsonResponse
    {
        try {
            $invoices = $this->clientService->getClientInvoices($id);
            return $this->successResponse('Invoices retrieved successfully', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showProfile(): JsonResponse
    {
        try {
            $client = $this->clientService->getCurrentClient();
            return $this->successResponse('Profile retrieved successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateProfile(UpdateClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->updateCurrentClient($request->validated());
            return $this->successResponse('Profile updated successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
