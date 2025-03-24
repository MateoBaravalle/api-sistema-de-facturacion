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

    public function getAllInvoices(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->model->query(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getMyInvoices(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->getMyThing(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getInvoiceById(int $id): Invoice
    {
        return $this->getById($id);
    }

    public function getMyInvoiceById(int $invoiceId): Invoice
    {
        if (!$this->belongsMe($invoiceId)) {
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
