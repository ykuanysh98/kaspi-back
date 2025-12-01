<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Тек авторизацияланған қолданушылар ғана
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|max:2048', // max 2MB
        ];
    }
}
