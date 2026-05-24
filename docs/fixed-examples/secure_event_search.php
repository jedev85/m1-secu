<?php

// Exemple pedagogique: parametrer la recherche au lieu de concatener l'entree utilisateur.
$sql = 'SELECT * FROM event WHERE published = true AND (LOWER(title) LIKE LOWER(:term) OR LOWER(location) LIKE LOWER(:term)) ORDER BY starts_at ASC';
$rows = $connection->fetchAllAssociative($sql, ['term' => '%'.$term.'%']);
