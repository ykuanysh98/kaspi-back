<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушылар ғана
        return auth()->check();
    }

    public function rules(): array
    {
        $partnerId = $this->route('id'); // URL параметрі

        return [
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:partners,email,' . $partnerId,
            'phone'        => 'nullable|string|max:50',
            'address'      => 'nullable|string|max:255',
            'password'     => 'nullable|string|min:6',
            'role'         => 'nullable|string',
        ];
    }
}
