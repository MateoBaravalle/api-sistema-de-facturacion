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
        return $this->getById($id);
    }

    public function getInvoicesByClient(int $clientId, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        // $cacheKey = $this->getCacheKey('client', $clientId . $page . $perPage);
        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->paginate(
        //         $this->model->where('client_id', $clientId),
        //         $page,
        //         $perPage
        //     )
        // );
        return $this->paginate(
            $this->model->query()->where('client_id', $clientId),
            $page,
            $perPage
        );
    }

    public function getInvoicesByStatus(string $status, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        // $cacheKey = $this->getCacheKey('status', $status . $page . $perPage);
        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->paginate(
        //         $this->model->where('status', $status),
        //         $page,
        //         $perPage
        //     )
        // );
        return $this->paginate(
            $this->model->query()->where('status', $status),
            $page,
            $perPage
        );
    }

    public function getMyInvoices(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $client = auth()->user()->client;

        if (!$client) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        return $this->getInvoicesByClient($client->id, $page, $perPage);
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
        // $this->clearModelCache($invoice->id, [self::MODEL]);
        return $this->create($data);
    }

    public function updateInvoice(int $id, array $data): Invoice
    {
        // $this->clearModelCacheWithSuffixes(
        //     $id,
        //     ['invoice', 'client', 'order'],
        //     ['pending', 'completed']
        // );
            
        return $this->update($id, $data);
    }

    public function deleteInvoice(int $id): bool
    {
        // if ($deleted) {
        //     $this->clearModelCacheWithSuffixes(
        //         $id,
        //         ['invoice', 'client', 'order'],
        //         ['pending', 'completed']
        //     );
        // }
                
        return $this->delete($id);
    }
}
