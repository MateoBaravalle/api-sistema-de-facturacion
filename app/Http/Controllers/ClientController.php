<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Http\Requests\ClientRequest\StoreClientRequest;
use App\Http\Requests\ClientRequest\UpdateClientRequest;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller {
    protected ClientService $clientService;

    public function __construct(ClientService $clientService) {
        $this->clientService = $clientService;
    }

    private function successResponse(string $message, array $data = [], int $code = 200): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            ...$data
        ], $code);
    }

    private function errorResponse(string $message, ?string $error = null, int $code = 400): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $error
        ], $code);
    }

    public function index(): JsonResponse {
        try {
            $clients = $this->clientService->getAllClients();
            return $this->successResponse('Clients retrieved successfully', ['clients' => $clients]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve clients', $e->getMessage());
        }
    }

    public function store(StoreClientRequest $request): JsonResponse {
        try {
            $client = $this->clientService->createClient($request->validated());
            return $this->successResponse('Client created successfully', ['client' => $client], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create client', $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse {
        try {
            $client = $this->clientService->getClientById($id);
            return $this->successResponse('Client retrieved successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve client', $e->getMessage());
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse {
        try {
            $client = $this->clientService->updateClient($id, $request->validated());
            return $this->successResponse('Client updated successfully', ['client' => $client]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update client', $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse {
        try {
            $this->clientService->deleteClient($id);
            return $this->successResponse('Client deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete client', $e->getMessage());
        }
    }
}
