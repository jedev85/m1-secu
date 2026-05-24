# Session 06 - API et SSRF

## Objectifs pedagogiques
Auditer une API JSON et une fonctionnalite serveur qui recupere une URL.

## Contexte
L'API expose evenements, utilisateurs, factures et preview URL.

## Notions abordees
BOLA, schema de reponse, CORS, SSRF, allowlist, filtrage IP.

## Parcours dans l'application
`/api/events`, `/api/users/{id}`, `/api/invoices/{id}`, `/api/preview-url`.

## Exercices etudiants
N1: inventorier endpoints. N2: identifier donnees excessives. N3: restreindre autorisations et sorties. N4: bloquer IP privees/locales. N5: fiche API.

## Indices progressifs
Comparer utilisateur courant et ID demande.

## Points de vigilance
Tester uniquement avec services locaux controles.

## Liens OWASP/CWE
OWASP API1 BOLA, API3 Excessive Data Exposure, CWE-918.

## Livrable attendu
Rapport API court et patch SSRF.
