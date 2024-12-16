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

    public function getAll(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $products = $this->productService->getAllProducts($page, $perPage);
            return $this->successResponse('Productos recuperados', ['productos' => $products]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());
            return $this->successResponse('Producto creado', ['producto' => $product], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());
            return $this->successResponse('Producto actualizado', ['producto' => $product]);
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
