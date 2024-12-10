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
            return $this->successResponse('Invoices retrieved successfully', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByClient(int $id): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getInvoicesByClient($id);
            return $this->successResponse('Invoices retrieved successfully', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');
            $invoices = $this->invoiceService->getInvoicesByStatus($status);
            return $this->successResponse('Invoices retrieved successfully', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyInvoices(): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getMyInvoices();
            return $this->successResponse('Invoices retrieved successfully', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($id);
            return $this->successResponse('Invoice retrieved successfully', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice($request->validated());
            return $this->successResponse('Invoice created successfully', ['invoice' => $invoice], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice($id, $request->validated());
            return $this->successResponse('Invoice updated successfully', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($id);
            return $this->successResponse('Invoice deleted successfully');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
