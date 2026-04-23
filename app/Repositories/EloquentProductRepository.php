<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentProductRepository implements ProductRepositoryInterface
{
    private const SORT_PRICE_ASC = 'price_asc';

    private const SORT_PRICE_DESC = 'price_desc';

    private const SORT_RATING_DESC = 'rating_desc';

    public function paginate(array $filters, string $sort, int $perPage): LengthAwarePaginator
    {
        $query = Product::query()
            ->when($filters['q'] ?? null, fn (Builder $query, string $q) => $query->where('name', 'like', "%{$q}%"))
            ->when(array_key_exists('price_from', $filters), fn (Builder $query) => $query->where('price', '>=', $filters['price_from']))
            ->when(array_key_exists('price_to', $filters), fn (Builder $query) => $query->where('price', '<=', $filters['price_to']))
            ->when(array_key_exists('category_id', $filters), fn (Builder $query) => $query->where('category_id', $filters['category_id']))
            ->when(array_key_exists('in_stock', $filters), fn (Builder $query) => $query->where('in_stock', $filters['in_stock']))
            ->when(array_key_exists('rating_from', $filters), fn (Builder $query) => $query->where('rating', '>=', $filters['rating_from']));

        match ($sort) {
            self::SORT_PRICE_ASC => $query->orderBy('price'),
            self::SORT_PRICE_DESC => $query->orderByDesc('price'),
            self::SORT_RATING_DESC => $query->orderByDesc('rating'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
