<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService extends Service
{
    protected const MODEL = 'order';

    public function __construct(Order $order)
    {
        parent::__construct($order, self::MODEL);
    }

    public function getAllOrders(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->model->query(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getMyOrders(array $params): LengthAwarePaginator
    {
        $query = $this->getFilteredAndSorted(
            $this->getMyThing(),
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }
    
    public function getOrderById(int $id): Order
    {
        return $this->getByIdWith($id, ['products', 'invoice']);
    }

    public function getMyOrderById(int $orderId): Order
    {
        if (!$this->belongsMe($orderId)) {
            throw new AuthorizationException('La orden no pertenece al cliente actual');
        }

        // $cacheKey = $this->getCacheKey('id', $orderId);
        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->getOrderById($orderId)
        // );
        return $this->getOrderById($orderId);
    }

    public function createOrder(array $data): Order
    {
        $products = $data['products'];
        unset($data['products']);

        $order = $this->create($data);
        $order->products()->attach($products);

        // $this->clearModelCache($order->id, [self::MODEL]);

        return $order->load('products');
    }

    public function createMyOrder(array $data): Order
    {
        $client = auth()->user()->client;

        if (!$client) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        $data['client_id'] = $client->id;
        
        return $this->createOrder($data);
    }

    public function updateOrder(int $id, array $data): Order
    {
        // $this->clearModelCacheWithSuffixes(
        //     $id,
        //     ['order', 'client', 'order'],
        //     ['pending', 'completed']
        // );
            
        return $this->update($id, $data);
    }

    public function updateMyOrder(int $id, array $data): Order
    {
        if (!$this->belongsMe($id)) {
            throw new AuthorizationException('La orden no pertenece al cliente actual');
        }

        return $this->updateOrder($id, $data);
    }

    public function deleteOrder(int $id): bool
    {
        // if ($deleted) {
        //     $this->clearModelCacheWithSuffixes(
        //         $id,
        //         ['order', 'client', 'order'],
        //         ['pending', 'completed']
        //     );
        // }
                
        return $this->delete($id);
    }

    public function getAverageOrderAmount(int $clientId): float
    {
        return $this->model->where('client_id', $clientId)->avg('amount');
    }
}
