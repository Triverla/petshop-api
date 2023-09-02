<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class AddressRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (!is_array($value) || !isset($value['billing']) || !isset($value['shipping'])) {
            return false;
        }

        return is_string($value['billing']) && is_string($value['shipping']);
    }

    public function message(): string
    {
        return 'The :attribute must be an array with "billing" and "shipping" as string values.';
    }
}
