<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,online',
            'coupon_code' => 'sometimes|string|exists:coupons,code',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Payment method must be either COD or Online',
            'coupon_code.exists' => 'Invalid coupon code',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray())
        );
    }
}
