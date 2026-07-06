<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'url' => 'The :attribute field must be a valid URL.',
    'string' => 'The :attribute field must be a string.',
    'alpha_num' => 'The :attribute field may only contain letters and numbers.',
    'numeric' => 'The :attribute field must be a number.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'enum' => 'The selected :attribute is invalid.',
    'min' => [
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'size' => [
        'numeric' => 'The :attribute field must be :size.',
        'string' => 'The :attribute field must be :size characters.',
    ],
    'currency_unsupported' => 'The provided currency is not supported.',
];
