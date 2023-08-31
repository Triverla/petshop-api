<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'avatar' => 'nullable',
            'email' => 'email|unique:users',
            'address' => 'string',
            'phone_number' => 'string',
            'is_marketing' => 'boolean|nullable',
            'is_admin' => 'boolean|nullable',
        ];
    }

    public function safe(?array $keys = null): ValidatedInput|array
    {
        $data = parent::safe($keys);
        if (is_null($this->input('is_marketing'))) {
            $data['is_marketing'] = 0;
        }

        if (is_null($this->input('is_admin'))) {
            $data['is_admin'] = 0;
        }

        return $data;
    }
}
