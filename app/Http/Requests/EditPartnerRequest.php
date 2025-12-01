<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған партнер ғана
        return Auth::guard('sanctum')->check();
    }

    public function rules(): array
    {
        $partnerId = $this->user('sanctum')->id;

        return [
            'company_name' => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:partners,email,' . $partnerId,
            'password'     => 'sometimes|string|min:6|confirmed',
        ];
    }
}
