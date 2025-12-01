<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // тек авторизацияланған қолданушылар
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'partner_id' => 'required|exists:partners,id',
            'quantity'   => 'required|integer|min:1',
            'price'      => 'nullable|integer',
        ];
    }
}
