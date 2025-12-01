<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушы
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'partner_id' => 'required|array',
            'partner_id.*' => 'exists:partners,id',
            'price' => 'nullable|numeric',
            'quantity' => 'nullable|integer|min:0',
        ];
    }
}
