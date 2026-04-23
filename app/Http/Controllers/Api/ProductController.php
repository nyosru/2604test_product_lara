<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'List products',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'q', description: 'Search by substring in product name', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'price_from', in: 'query', schema: new OA\Schema(type: 'number', minimum: 0)),
            new OA\Parameter(name: 'price_to', in: 'query', schema: new OA\Schema(type: 'number', minimum: 0)),
            new OA\Parameter(name: 'category_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'in_stock', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'rating_from', in: 'query', schema: new OA\Schema(type: 'number', minimum: 0, maximum: 5)),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ProductIndexRequest::SORTS)),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated product list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Product')
                        ),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function index(ProductIndexRequest $request)
    {
        $filters = $request->validated();

        $query = Product::query()
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('name', 'like', "%{$q}%"))
            ->when(array_key_exists('price_from', $filters), fn (Builder $query) => $query->where('price', '>=', $filters['price_from']))
            ->when(array_key_exists('price_to', $filters), fn (Builder $query) => $query->where('price', '<=', $filters['price_to']))
            ->when(array_key_exists('category_id', $filters), fn (Builder $query) => $query->where('category_id', $filters['category_id']))
            ->when(array_key_exists('in_stock', $filters), fn (Builder $query) => $query->where('in_stock', $filters['in_stock']))
            ->when(array_key_exists('rating_from', $filters), fn (Builder $query) => $query->where('rating', '>=', $filters['rating_from']));

        match ($request->sort()) {
            ProductIndexRequest::SORT_PRICE_ASC => $query->orderBy('price'),
            ProductIndexRequest::SORT_PRICE_DESC => $query->orderByDesc('price'),
            ProductIndexRequest::SORT_RATING_DESC => $query->orderByDesc('rating'),
            default => $query->latest(),
        };

        return ProductResource::collection(
            $query->paginate($request->perPage())->withQueryString()
        );
    }
}
