<?php

declare(strict_types=1);

return [
    'connection_failed' => 'Unable to reach the payment gateway.',
    'malformed_response' => 'An invalid response was received from the gateway.',
    'json_encode_failed' => 'Unable to encode the request payload as JSON.',
    'http_error' => 'The gateway responded with HTTP status :status (:reason).',
    'config' => [
        'missing' => 'The configuration key ":key" is missing.',
        'invalid_environment' => 'The environment ":value" is invalid.',
        'missing_base_url' => 'No base URL is configured for the ":env" environment.',
        'invalid_value' => 'The value ":value" is invalid for the configuration key ":key".',
    ],
    'validation' => [
        'failed' => 'The request validation failed.',
    ],
    'gateway' => [
        '1' => 'The order number has already been used.',
        '5' => 'Access denied (invalid credentials).',
        'unknown' => 'An unexpected error occurred.',
    ],
];
