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

    protected function getAllowedFilters(): array
    {
        return [
            'name',
            'cuit',
            'email',
            'phone',
            'address',
            'city',
            'province',
            'credit_limit',
            'balance',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $clients = $this->clientService->getAllClients(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Clientes recuperados', $clients);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClientById($id);
            return $this->successResponse('Cliente recuperado', $client);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient(
                $request->validated()
            );
            
            return $this->successResponse('Cliente creado', $client, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient(
                $id,
                $request->validated()
            );
            return $this->successResponse('Cliente actualizado', $client);
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

    public function storeProfile(StoreClientRequest $request): JsonResponse
    {
        try {
            if (auth()->user()->client) {
                return $this->errorResponse('El usuario ya tiene un cliente asociado', 422);
            }

            $validated = $request->validated();
            $validated['user_id'] = auth()->user()->id;

            $client = $this->clientService->createClient($validated);
            return $this->successResponse('Cliente creado', $client, 201);
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
        
        $validated = $request->validated();
        unset($validated['credit_limit']);
        
        $newRequest = UpdateClientRequest::createFrom($request);
        $newRequest->replace($validated);
        
        return $this->update($newRequest, $client->id);
    }
}
