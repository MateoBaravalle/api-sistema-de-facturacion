<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends Service
{
    protected const MODEL = 'product';

    public function __construct(Product $product)
    {
        parent::__construct($product, self::MODEL);
    }

    public function getAllProducts(int $page, int $perPage = self::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->getAll($page, $perPage);
    }

    public function getProductById(int $id): Product
    {
        return $this->getById($id);
    }

    public function createProduct(array $data): Product
    {
        return $this->create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        return $this->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->delete($id);
    }
}
