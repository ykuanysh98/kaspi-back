<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DecreaseCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушылар ғана
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'partner_id' => 'required|exists:partners,id',
            'quantity' => 'sometimes|integer|min:1', // сан болмауы мүмкін, онда 1-ге азайтады
        ];
    }
}
