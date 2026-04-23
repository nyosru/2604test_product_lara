<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Products API',
    description: 'Search products with filters, sorting and pagination.'
)]
#[OA\Server(
    url: '',
    description: 'now domain'
)]
#[OA\Schema(
    schema: 'Product',
    required: ['id', 'name', 'price', 'category_id', 'in_stock', 'rating', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Apple iPhone 15'),
        new OA\Property(property: 'price', type: 'string', example: '999.99'),
        new OA\Property(property: 'category_id', type: 'integer', example: 2),
        new OA\Property(property: 'in_stock', type: 'boolean', example: true),
        new OA\Property(property: 'rating', type: 'number', format: 'float', minimum: 0, maximum: 5, example: 4.7),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'PaginationLink',
    properties: [
        new OA\Property(property: 'url', type: 'string', nullable: true),
        new OA\Property(property: 'label', type: 'string'),
        new OA\Property(property: 'active', type: 'boolean'),
    ],
    type: 'object'
)]
class OpenApiSpec {}
