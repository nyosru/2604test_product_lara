<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public const SORT_PRICE_ASC = 'price_asc';

    public const SORT_PRICE_DESC = 'price_desc';

    public const SORT_RATING_DESC = 'rating_desc';

    public const SORT_NEWEST = 'newest';

    public const SORTS = [
        self::SORT_PRICE_ASC,
        self::SORT_PRICE_DESC,
        self::SORT_RATING_DESC,
        self::SORT_NEWEST,
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('in_stock')) {
            return;
        }

        $value = filter_var($this->query('in_stock'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $this->merge([
            'in_stock' => $value,
        ]);
    }

    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:255'],
            'price_from' => ['sometimes', 'numeric', 'min:0'],
            'price_to' => [
                'sometimes',
                'numeric',
                'min:0',
                Rule::when($this->has('price_from'), ['gte:price_from']),
            ],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'in_stock' => ['sometimes', 'boolean'],
            'rating_from' => ['sometimes', 'numeric', 'between:0,5'],
            'sort' => ['sometimes', Rule::in(self::SORTS)],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function sort(): string
    {
        return $this->validated('sort', self::SORT_NEWEST);
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 15);
    }
}
