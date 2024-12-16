<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService extends Service
{
    protected const MODEL = 'payment';

    public function __construct(Payment $payment)
    {
        parent::__construct($payment, self::MODEL);
    }

    public function getAllPayments(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }

    public function getPaymentById(int $id): Payment
    {
        return $this->getById($id);
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
