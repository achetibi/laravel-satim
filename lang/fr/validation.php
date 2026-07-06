<?php

declare(strict_types=1);

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'alpha_num' => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'decimal' => 'Le champ :attribute doit comporter :decimal décimales.',
    'enum' => 'La valeur sélectionnée pour :attribute est invalide.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être au minimum de :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'max' => [
        'numeric' => 'Le champ :attribute ne peut pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne peut pas contenir plus de :max caractères.',
    ],
    'size' => [
        'numeric' => 'Le champ :attribute doit être égal à :size.',
        'string' => 'Le champ :attribute doit contenir :size caractères.',
    ],
    'currency_unsupported' => 'La devise fournie n’est pas prise en charge.',
];
