# Guide FOAD

La FOAD sert a consolider, rediger et preparer la seance suivante. Elle ne doit pas dependre d'un service externe obligatoire.

## Format attendu

Chaque rendu FOAD tient en une a deux pages ou en un commit court:

- resume du travail realise;
- captures ou extraits minimaux;
- analyse du risque;
- proposition de correction;
- questions restantes.

## Calendrier

| Apres session | Travail FOAD |
| --- | --- |
| 01 | Completer la cartographie OWASP et API |
| 02 | Comparer trois corrections SQLi |
| 03 | Checklist Twig + CSRF |
| 04 | Definition of done securite |
| 05 | Matrice d'autorisation |
| 06 | Schema API et tests BOLA/SSRF |
| 07 | Plan secrets/logs/hardening |
| 08 | Analyse supply chain |
| 09 | Note memoire et ASan |
| 10 | Mini plan fuzzing |
| 11 | Consolidation des constats |
| 12 | Rapport final et soutenance |

## Criteres communs

Un bon rendu FOAD:

- reste dans le perimetre local;
- separe clairement observation, interpretation et correction;
- evite les payloads destructeurs;
- cite les fichiers ou routes concernes;
- propose une verification concrete.

## Journal d'audit

Tenir un journal continu dans un fichier personnel non commite ou dans un document de groupe:

```text
Date:
Route/fichier:
Observation:
Hypothese:
Preuve locale:
Impact:
Correction envisagee:
Verification:
Statut:
```

Ce journal sert de base pour la session 11 et l'audit final.
