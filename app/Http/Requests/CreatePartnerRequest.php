<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушылар (мысалы admin) рұқсат алады
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:partners,email',
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
            'password'     => 'required|string|min:6',
        ];
    }
}
