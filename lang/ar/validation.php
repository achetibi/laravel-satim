<?php

declare(strict_types=1);

return [
    'required' => 'حقل :attribute مطلوب.',
    'url' => 'يجب أن يكون حقل :attribute رابطاً صحيحاً.',
    'string' => 'يجب أن يكون حقل :attribute نصاً.',
    'alpha_num' => 'يجب ألا يحتوي حقل :attribute إلا على حروف وأرقام.',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'decimal' => 'يجب أن يحتوي حقل :attribute على :decimal منازل عشرية.',
    'enum' => 'القيمة المحددة للحقل :attribute غير صحيحة.',
    'min' => [
        'numeric' => 'يجب ألا تقل قيمة حقل :attribute عن :min.',
        'string' => 'يجب ألا يقل حقل :attribute عن :min أحرف.',
    ],
    'max' => [
        'numeric' => 'يجب ألا تزيد قيمة حقل :attribute عن :max.',
        'string' => 'يجب ألا يزيد حقل :attribute عن :max أحرف.',
    ],
    'size' => [
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يحتوي حقل :attribute على :size أحرف.',
    ],
    'currency_unsupported' => 'العملة المقدمة غير مدعومة.',
];
