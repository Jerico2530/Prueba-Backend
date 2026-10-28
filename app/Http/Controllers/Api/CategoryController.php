<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CategoryController extends Controller
{
    use ApiResponse;

    #[OA\Get(
        path: '/api/categories',
        summary: 'Listar categorías',
        tags: ['Categories'],
        responses: [new OA\Response(response: 200, description: 'Listado de categorías')]
    )]
    public function index(): JsonResponse
    {
        $categories = Category::latest()->get();

        return $this->success(CategoryResource::collection($categories), 'Categorías obtenidas correctamente.');
    }

    #[OA\Post(
        path: '/api/categories',
        summary: 'Crear categoría',
        tags: ['Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ropa'),
                    new OA\Property(property: 'description', type: 'string', example: 'Categoría de ropa'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Categoría creada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return $this->success(new CategoryResource($category), 'Categoría creada correctamente.', 201);
    }

    #[OA\Get(
        path: '/api/categories/{id}',
        summary: 'Ver una categoría',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Categoría encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show(Category $category): JsonResponse
    {
        return $this->success(new CategoryResource($category), 'Categoría obtenida correctamente.');
    }

    #[OA\Put(
        path: '/api/categories/{id}',
        summary: 'Actualizar categoría',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ropa'),
                    new OA\Property(property: 'description', type: 'string', example: 'Categoría de ropa'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Categoría actualizada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success(new CategoryResource($category), 'Categoría actualizada correctamente.');
    }

    #[OA\Delete(
        path: '/api/categories/{id}',
        summary: 'Eliminar categoría',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Categoría eliminada'),
            new OA\Response(response: 409, description: 'Tiene productos asociados'),
        ]
    )]
    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            throw new HttpException(409, 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return $this->success(null, 'Categoría eliminada correctamente.');
    }
}
