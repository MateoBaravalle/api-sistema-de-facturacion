<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService extends Service
{
    protected const MODEL = 'invoice';

    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice, self::MODEL);
    }

    public function getAllInvoices(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }

    public function getInvoiceById(int $id): Invoice
    {
        return $this->getById($id, self::MODEL);
    }

    public function getInvoicesByClient(int $clientId, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey('client', $clientId . $page . $perPage);
        return $this->remember(
            $cacheKey,
            fn () => $this->paginate(
                $this->model->where('client_id', $clientId),
                $page,
                $perPage
            )
        );
    }

    public function getInvoicesByStatus(string $status, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey('status', $status . $page . $perPage);
        return $this->remember(
            $cacheKey,
            fn () => $this->paginate(
                $this->model->where('status', $status),
                $page,
                $perPage
            )
        );
    }

    public function getMyInvoices(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $clientId = auth()->user()->client->id;

        if (!$clientId) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        return $this->getInvoicesByClient($clientId, $page, $perPage);
    }

    public function getMyInvoiceById(int $invoiceId): Invoice
    {
        if (!$this->belongsToClient($invoiceId)) {
            throw new AuthorizationException('La factura no pertenece al cliente actual');
        }

        return $this->getInvoiceById($invoiceId);
    }

    public function createInvoice(array $data): Invoice
    {
        $invoice = $this->create($data);
        // $this->clearModelCache($invoice->id, [self::MODEL]);
        return $invoice;
    }

    public function updateInvoice(int $id, array $data): Invoice
    {
        $invoice = $this->getInvoiceById($id);
        $invoice->update($data);
        
        // $this->clearModelCacheWithSuffixes(
        //     $id,
        //     ['invoice', 'client', 'order'],
        //     ['pending', 'completed']
        // );
        
        return $invoice->fresh();
    }

    public function deleteInvoice(int $id): bool
    {
        $deleted = $this->getInvoiceById($id)->delete();

        // if ($deleted) {
        //     $this->clearModelCacheWithSuffixes(
        //         $id,
        //         ['invoice', 'client', 'order'],
        //         ['pending', 'completed']
        //     );
        // }

        return $deleted;
    }
}
