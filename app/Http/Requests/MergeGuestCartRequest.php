<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeGuestCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'guest_cart' => 'required|array',
            'guest_cart.*.product_id' => 'required|exists:products,id',
            'guest_cart.*.partner_id' => 'required|exists:partners,id',
            'guest_cart.*.quantity' => 'required|integer|min:1'
        ];
    }
}
