# Session 06 - API et SSRF

## Objectifs pédagogiques
Auditer une API JSON et une fonctionnalité serveur qui récupère une URL.

## Contexte
L'API expose événements, utilisateurs, factures et preview URL.

## Notions abordées
BOLA, schémà de réponse, CORS, SSRF, allowlist, filtrage IP.

## Parcours dans l'application
`/api/events`, `/api/users/{id}`, `/api/invoices/{id}`, `/api/preview-url`.

## Exercices étudiants
N1: inventorier endpoints. N2: identifier données excessives. N3: restreindre autorisations et sorties. N4: bloquer IP privées/locales. N5: fiche API.

## Indices progressifs
Comparer utilisateur courant et ID demandé.

## Points de vigilance
Tester uniquement avec services locaux contrôlés.

## Liens OWASP/CWE
OWASP API1 BOLA, API3 Excessive Datà Exposure, CWE-918.

## Livrable attendu
Rapport API court et patch SSRF.
