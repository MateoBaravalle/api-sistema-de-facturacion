<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService extends Service
{
    protected const MODEL = 'invoice';

    public function __construct(Invoice $invoice)
    {
        parent::__construct($invoice, self::MODEL);
    }

    public function getAllInvoices(int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->remember(
            $this->getCacheKey('all'),
            fn () => $this->paginate($this->model->query(), $perPage)
        );
    }

    public function getInvoiceById(int $id): Invoice
    {
        return $this->getById($id, self::MODEL);
    }

    public function getInvoicesByClient(int $clientId, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey("client_id.{$clientId}");
        return $this->remember(
            $cacheKey,
            fn () => $this->paginate(
                $this->model->where('client_id', $clientId),
                $perPage
            )
        );
    }

    public function getInvoicesByStatus(string $status, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        $cacheKey = $this->getCacheKey("status.{$status}");
        return $this->remember(
            $cacheKey,
            fn () => $this->paginate(
                $this->model->where('status', $status),
                $perPage
            )
        );
    }

    public function getMyInvoices(): LengthAwarePaginator
    {
        return $this->getInvoicesByClient(auth()->user()->client->id);
    }

    public function createInvoice(array $data): Invoice
    {
        $invoice = $this->create($data);
        $this->clearModelCache($invoice->id, [self::MODEL]);
        return $invoice;
    }

    public function updateInvoice(int $id, array $data): Invoice
    {
        $invoice = $this->getInvoiceById($id);
        $invoice->update($data);
        
        $this->clearModelCacheWithSuffixes(
            $id,
            ['invoice', 'client', 'order'],
            ['pending', 'completed']
        );
        
        return $invoice->fresh();
    }

    public function deleteInvoice(int $id): bool
    {
        $deleted = $this->getInvoiceById($id)->delete();

        if ($deleted) {
            $this->clearModelCacheWithSuffixes(
                $id,
                ['invoice', 'client', 'order'],
                ['pending', 'completed']
            );
        }

        return $deleted;
    }
}