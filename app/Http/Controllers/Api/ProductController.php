<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: '/api/products',
        summary: 'Listar productos',
        tags: ['Products'],
        responses: [new OA\Response(response: 200, description: 'Listado de productos')]
    )]
    public function index(): JsonResponse
    {
        $products = Product::with('category')->latest()->get();

        return $this->success(ProductResource::collection($products), 'Productos obtenidos correctamente.');
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Crear producto',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull'),
                    new OA\Property(property: 'description', type: 'string', example: 'Camiseta negra edición limitada'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.90),
                    new OA\Property(property: 'stock', type: 'integer', example: 50),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated())->load('category');

        return $this->success(new ProductResource($product), 'Producto creado correctamente.', 201);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Ver un producto',
        tags: ['Products'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Producto encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        return $this->success(new ProductResource($product->load('category')), 'Producto obtenido correctamente.');
    }

    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Actualizar producto',
        tags: ['Products'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull'),
                    new OA\Property(property: 'description', type: 'string', example: 'Camiseta negra edición limitada'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.90),
                    new OA\Property(property: 'stock', type: 'integer', example: 50),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return $this->success(new ProductResource($product->load('category')), 'Producto actualizado correctamente.');
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Eliminar producto',
        tags: ['Products'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Producto eliminado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return $this->success(null, 'Producto eliminado correctamente.');
    }
}
