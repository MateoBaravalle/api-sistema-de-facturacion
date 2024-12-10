<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest\StoreInvoiceRequest;
use App\Http\Requests\InvoiceRequest\UpdateInvoiceRequest;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $invoices = $this->invoiceService->getAllInvoices($page, $perPage);
            return $this->successResponse('Facturas recuperadas', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(int $id, Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $invoices = $this->invoiceService->getInvoicesByClient($id, $page, $perPage);
            return $this->successResponse('Facturas recuperadas', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $invoices = $this->invoiceService->getInvoicesByStatus($status, $page, $perPage);
            return $this->successResponse('Facturas recuperadas', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyInvoices(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $invoices = $this->invoiceService->getMyInvoices($page, $perPage);
            return $this->successResponse('Facturas recuperadas', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);
            return $this->successResponse('Factura recuperada', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());
            return $this->successResponse('Factura creada', ['invoice' => $invoice], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());
            return $this->successResponse('Factura actualizada', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            return $this->successResponse('Factura eliminada');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
