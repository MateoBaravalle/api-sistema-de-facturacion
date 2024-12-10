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

    public function getAllOrders(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }
    
    public function getOrderById(int $id): Order
    {
        return $this->getByIdWith($id, ['products', 'invoice']);
    }

    public function getOrdersByClient(int $clientId, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        // $cacheKey = $this->getCacheKey('client', $clientId . $page . $perPage);
        // return $this->remember(
        //     $cacheKey,
        //     fn () => $this->paginate(
        //         $this->model->with('products', 'invoice')->where('client_id', $clientId),
        //         $page,
        //         $perPage
        //     )
        // );
        return $this->paginate(
            $this->model->with('products', 'invoice')->where('client_id', $clientId),
            $page,
            $perPage
        );
    }

    public function getOrdersByStatus(string $status, int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
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
            $this->model->where('status', $status),
            $page,
            $perPage
        );
    }

    public function getMyOrderById(int $orderId): Order
    {
        if (!$this->belongsToClient($orderId)) {
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
        $clientId = auth()->user()->client->id;

        if (!$clientId) {
            throw new AuthorizationException('Cliente no encontrado');
        }

        $data['client_id'] = $clientId;
        
        return $this->createOrder($data);
    }

    public function updateOrder(int $id, array $data): Order
    {
        $order = $this->getById($id);
        $order->update($data);
        
        // $this->clearModelCacheWithSuffixes(
        //     $id,
        //     ['order', 'client', 'order'],
        //     ['pending', 'completed']
        // );
        
        return $order->fresh();
    }

    public function updateMyOrder(int $id, array $data): Order
    {
        if (!$this->belongsToClient($id)) {
            throw new AuthorizationException('La orden no pertenece al cliente actual');
        }

        $order = $this->getById($id);
        $order->update($data);
        return $order->fresh();
    }

    public function deleteOrder(int $id): bool
    {
        $deleted = $this->getById($id)->delete();

        // if ($deleted) {
        //     $this->clearModelCacheWithSuffixes(
        //         $id,
        //         ['order', 'client', 'order'],
        //         ['pending', 'completed']
        //     );
        // }

        return $deleted;
    }
}
