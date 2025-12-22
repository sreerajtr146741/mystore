<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive',
            'discount' => 'sometimes|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category does not exist',
            'discount.max' => 'Discount cannot exceed 100%',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray())
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            ApiResponse::forbidden('Only admins can update products')
        );
    }
}
