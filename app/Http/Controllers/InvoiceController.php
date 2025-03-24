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

    protected function getAllowedFilters(): array
    {
        return [
            'client_id',
            'order_id',
            'status',
            'date',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getAllInvoices(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Facturas recuperadas', ['invoices' => $invoices]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function getMyInvoices(Request $request): JsonResponse
    {
        try {
            $invoices = $this->invoiceService->getMyInvoices(
                $this->getQueryParams($request)
            );

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
            $invoice = $this->invoiceService->createInvoice(
                $request->validated()
            );

            return $this->successResponse('Factura creada', ['invoice' => $invoice], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice(
                $id,
                $request->validated()
            );

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
