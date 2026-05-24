<?php

// Exemple pedagogique: allowlist explicite au lieu d'appeler tous les setters fournis par JSON.
$allowed = ['fullName', 'company', 'phone'];
foreach ($allowed as $field) {
    if (array_key_exists($field, $payload)) {
        $setter = 'set'.ucfirst($field);
        $user->{$setter}((string) $payload[$field]);
    }
}
