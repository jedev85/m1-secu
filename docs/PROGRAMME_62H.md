# Programme detaille 62h

Ce document transforme EventSecure Lab en fil rouge exploitable sur un volume long. Le decoupage ci-dessous prevoit 42h de presentiel et 20h de FOAD, avec 1h de marge utilisable pour installation, rattrapage ou soutenance. Si le module est cale sur 42h + 21h, la derniere heure FOAD sert a finaliser le journal d'audit.

## Objectif du fil rouge

Les etudiants auditent progressivement une application Symfony realiste de gestion d'evenements. Le metier doit rester lisible: un utilisateur consulte des evenements, s'inscrit, commente, gere son profil, telecharge ses factures et interagit avec une API. Les failles ne sont pas des enigmes isolees: elles apparaissent dans des workflows metier concrets.

## Repartition horaire

| Session | Theme | Presentiel | FOAD | Livrable principal |
| --- | --- | ---: | ---: | --- |
| 01 | Cartographie OWASP | 3h30 | 1h30 | Carte surface d'attaque |
| 02 | SQL Injection | 3h30 | 1h30 | Patch recherche + test |
| 03 | XSS et CSRF | 3h30 | 2h | Correctif commentaires/suppression |
| 04 | SDLC et DevSecOps | 3h30 | 1h30 | Controle qualite securite |
| 05 | Auth et access control | 3h30 | 2h | Correctif facture/profil |
| 06 | API, BOLA et SSRF | 3h30 | 2h | Rapport API + patch SSRF |
| 07 | Secrets, logs, hardening | 3h30 | 1h30 | Plan de durcissement |
| 08 | Supply chain et CVE | 3h30 | 1h30 | Note d'analyse Composer |
| 09 | Concepts memoire | 3h30 | 1h30 | Analyse crash native-lab |
| 10 | Fuzzing intro | 3h30 | 1h30 | Campagne fuzzing courte |
| 11 | Methodologie audit | 3h30 | 2h | Dossier de constats priorises |
| 12 | Audit final | 3h30 | 2h | Rapport final + soutenance courte |

Total cible: 42h presentiel + 20h FOAD = 62h. La matiere excede volontairement ce volume: chaque session contient des niveaux optionnels pour groupes rapides.

## Rythme recommande par seance

Chaque seance presentielle suit la meme structure:

1. 20 min: rappel du contexte metier et du risque du jour.
2. 30 min: exploration guidee de l'application et du code.
3. 60 min: identification et preuve locale controlee.
4. 60 min: correction ou durcissement.
5. 25 min: verification, test, discussion d'impact.
6. 15 min: preparation du livrable et consignes FOAD.

Le formateur peut reduire la correction live si l'objectif est l'audit, ou au contraire reduire la preuve locale si l'objectif est le developpement securise.

## Livrables recurrents

Chaque livrable doit contenir:

- perimetre observe;
- symptome constate;
- cause racine probable;
- impact metier;
- preuve locale non destructive;
- proposition de correction;
- verification apres correction;
- niveau de confiance et limites.

## Progression attendue

En debut de module, on accepte des constats courts et descriptifs. A partir de la session 05, chaque constat doit distinguer symptome, cause et impact. A partir de la session 08, les etudiants doivent prioriser. Lors de l'audit final, un constat sans remediations verifiables est considere incomplet.

## Utilisation des branches

- `student`: support distribue aux etudiants. Il contient les failles, les exercices, les indices et les sujets.
- `teacher`: support formateur. Il contient les corrections, variantes, grilles et exemples de patch.

Ne pas distribuer `teacher` avant la fin du module.
