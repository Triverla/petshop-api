<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

final class JsonRule implements ValidationRule
{
    /**
     * @var array<string, array<string>|string>
     */
    private array $rules;

    /**
     * @param array<string, array<string>|string> $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $values = is_array($value) ? $value : json_decode($value, true);

        if (!is_array($values)) {
            $fail($attribute . ' must be valid JSON');

            return;
        }
        foreach ($values as $val) {
            if (array_diff(array_keys($this->rules), array_keys($val))) {
                $errors = implode(',', array_keys($this->rules));
                $fail($attribute . ' is not correct.' . $errors);

                return;
            }

            $validator = Validator::make($val, $this->rules);

            if ($validator->fails()) {
                $messages = $validator->errors()->all();
                $fail(implode('', $messages));
            }
        }
    }
}
