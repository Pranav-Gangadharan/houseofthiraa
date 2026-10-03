<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'category' => ['required', Rule::in(array_keys(config('shop.categories')))],
            'price' => ['required', 'integer', 'min:1', 'max:100000'],
            'description' => ['nullable', 'string', 'max:600'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => [Rule::in(config('shop.sizes'))],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'max:6144'],
            'remove' => ['nullable', 'array'],
            'remove.*' => ['string'],
        ];
    }
}
