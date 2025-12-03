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
            'name'        => 'nullable|string|max:255',
            'price'       => 'nullable|numeric',
            'quantity'    => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
        ];
    }
}
