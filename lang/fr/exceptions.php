<?php

declare(strict_types=1);

return [
    'connection_failed' => 'Impossible de contacter la passerelle de paiement.',
    'malformed_response' => 'Réponse invalide reçue de la passerelle.',
    'json_encode_failed' => 'Impossible d’encoder la charge utile de la requête au format JSON.',
    'http_error' => 'La passerelle a répondu avec le statut HTTP :status (:reason).',
    'config' => [
        'missing' => 'La clé de configuration « :key » est manquante.',
        'invalid_environment' => 'L’environnement « :value » est invalide.',
        'missing_base_url' => 'Aucune URL de base n’est configurée pour l’environnement « :env ».',
        'invalid_value' => 'La valeur « :value » est invalide pour la clé de configuration « :key ».',
    ],
    'validation' => [
        'failed' => 'La validation de la requête a échoué.',
    ],
    'gateway' => [
        '1' => 'Numéro de commande déjà utilisé.',
        '5' => 'Accès refusé (identifiants invalides).',
        'unknown' => 'Une erreur inattendue est survenue.',
    ],
];
