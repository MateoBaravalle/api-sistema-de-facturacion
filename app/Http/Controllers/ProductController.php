<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest\StoreProductRequest;
use App\Http\Requests\ProductRequest\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    protected function getAllowedFilters(): array
    {
        return [
            'supplier_id',
            'name',
            'category',
            'status',
        ];
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->getAllProducts(
                $this->getQueryParams($request)
            );

            return $this->successResponse('Productos recuperados', $products);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct(
                $request->validated()
            );

            return $this->successResponse('Producto creado', $product, 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct(
                $id,
                $request->validated()
            );

            return $this->successResponse('Producto actualizado', $product);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return $this->successResponse('Producto eliminado');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
