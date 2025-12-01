<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Кез келген қолданушы тіркелу мүмкін
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:partners,email',
            'password'     => 'required|string|min:6|confirmed',
        ];
    }
}
