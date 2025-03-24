<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService extends Service
{
    protected const MODEL = 'payment';

    public function __construct(Payment $payment)
    {
        parent::__construct($payment, self::MODEL);
    }

    public function getAllPayments(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->model->query(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getMyPayments(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->getMyThing(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getPaymentById(int $id): Payment
    {
        return $this->getById($id);
    }

    public function getMyPaymentById(int $id): Payment
    {
        if (!$this->belongsMe($id)) {
            throw new AuthorizationException('El pago no pertenece al cliente actual');
        }

        return $this->getPaymentById($id);
    }

    public function createPayment(array $data): Payment
    {
        return $this->create($data);
    }

    public function updatePayment(int $id, array $data): Payment
    {
        return $this->update($id, $data);
    }

    public function deletePayment(int $id): bool
    {
        return $this->delete($id);
    }
}
