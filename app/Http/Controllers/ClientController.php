<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest\StoreClientRequest;
use App\Http\Requests\ClientRequest\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Auth\Access\AuthorizationException;
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
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            $clients = $this->clientService->getAllClients($page, $perPage);
            return $this->successResponse('Clientes recuperados', ['clients' => $clients]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            return $this->successResponse('Cliente recuperado', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->successResponse('Cliente creado', ['client' => $client], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());
            return $this->successResponse('Cliente actualizado', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clientService->deleteClient($id);
            return $this->successResponse('Cliente eliminado');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showProfile(): JsonResponse
    {
        $client = auth()->user()->client;

        if (!$client) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        return $this->show($client->id);
    }

    public function updateProfile(UpdateClientRequest $request): JsonResponse
    {
        $client = auth()->user()->client;
        
        if (!$client) {
            throw new AuthorizationException('Cliente no encontrado');
        }
        
        return $this->update($request, $client->id);
    }
}

