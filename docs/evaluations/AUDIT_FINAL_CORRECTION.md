# Correction audit final

## Constats attendus

Minimum attendu:

- SQL Injection sur recherche evenement.
- XSS stockee sur commentaire.
- CSRF sur suppression commentaire ou changement email.
- IDOR sur facture.
- BOLA sur API users/invoices.
- Mass assignment sur profil API.
- Upload insuffisamment valide.
- SSRF sur preview URL.
- Logs contenant une donnee sensible fictive.
- Security misconfiguration: CORS large, headers incomplets, route admin secondaire.

## Grille detaillee

Voir `docs/GRADING_RUBRIC.md`.

## Exemples de remediation

Prioriser controles serveur, requetes parametrees, DTO, voters, validation stricte, logs redactes, headers et tests de regression.
