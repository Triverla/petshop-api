<?php

namespace App\Http\Requests\Api\V1\Order;

use App\Rules\JsonRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_status_uuid' => 'required|uuid|exists:order_statuses,uuid',
            'payment_uuid' => 'required|uuid|exists:payments,uuid',
            'products' => [
                'required',
                new JsonRule([
                    'product' => 'exists:products,uuid|string|required',
                    'quantity' => 'required|integer|min:1',
                ]),
            ],
            'address' => [
                'required',
                new JsonRule([
                    'billing' => 'required|string',
                    'shipping' => 'required|string',
                ]),
            ],
            'amount' => 'required|numeric',
        ];
    }
}
