<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Тек авторизацияланған қолданушы
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:products,name',
            'price'       => 'required|numeric',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status'      => 'string',
        ];
    }
}
