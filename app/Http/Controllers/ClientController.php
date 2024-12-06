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
            return $this->successResponse('Clients retrieved successfully', [...$clients]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            return $this->successResponse('Client retrieved successfully', [$client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->successResponse('Client created successfully', [$client], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());
            return $this->successResponse('Client updated successfully', [$client]);
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

    public function showProfile(): JsonResponse
    {
        return $this->show(auth()->id());
    }

    public function updateProfile(UpdateClientRequest $request): JsonResponse
    {
        return $this->update($request, auth()->id());
    }
}
