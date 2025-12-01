<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePartnerReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушылар ғана
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|min:3',
        ];
    }
}
