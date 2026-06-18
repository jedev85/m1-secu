# Diagnostic du support de TP

## 1. Synthese

Le projet est estime **confortable pour 24h**.

Il propose une application metier Symfony coherente, avec plusieurs workflows internes: gestion clients, support, documents, factures, projets, taches, notes, messages, webhooks, imports, exports, administration, API et journaux. Le volume fonctionnel permet une vraie demarche d'audit: cartographie, matrice des roles, analyse des routes, tests manuels, verification API, et production d'un rapport professionnel.

Ce document reste volontairement oriente support de cadrage. Il ne liste pas les reponses, payloads ou chemins d'exploitation attendus.

## 2. Cartographie fonctionnelle existante

| Module | Routes principales | Roles concernes | Actions | Donnees | Interet pedagogique |
| --- | --- | --- | --- | --- | --- |
| Authentification | `/login`, `/logout` | tous | connexion, deconnexion | comptes utilisateurs | analyser l'identification et les erreurs |
| Compte | `/account`, `/account/profile`, `/account/email`, `/account/password` | utilisateurs connectes | profil, email, mot de passe | donnees compte | tester actions sensibles |
| Clients | `/clients`, `/clients/{id}`, edition, suppression | user, support, manager, admin | CRUD | clients et proprietaires | matrice d'acces et ID internes |
| Tickets | `/tickets`, commentaires, statut, assignation | user, support, manager | support client | tickets, commentaires | workflow collaboratif |
| Documents | `/documents`, upload, download | tous roles connectes | depot, liste, telechargement | fichiers et metadonnees | audit upload et acces fichiers |
| Factures | `/invoices`, `/invoice/{id}/download`, exports | user, manager | consultation, export | factures et clients | donnees financieres |
| Projets | `/projects`, membres, statut, export | user, manager, admin | projet, budget, confidentialite | projets et membres | donnees confidentielles et workflows |
| Notes | `/notes` | user, support, manager | notes metier | contenu libre et visibilite | validation et exposition de contenu |
| Taches | `/tasks`, `/tasks/mine` | user, manager | assignation, statut, commentaires | taches projet | autorisations par affectation |
| Messages | `/messages` | tous roles connectes | inbox, envoi, lecture | messages prives | confidentialite inter-utilisateurs |
| Webhooks | `/webhooks` | manager, admin | creation, test, suppression | URL, secrets, evenements | integration sortante locale |
| Imports | `/import/url`, `/imports/csv` | user, manager | import URL/CSV | donnees externes | validation de sources |
| Administration | `/admin`, `/admin/users`, `/admin/settings`, `/admin/logs`, `/admin/activity` | manager, admin | roles, logs, parametres | utilisateurs, journaux, secrets | separation admin/metier |
| API | `/api/*` | connectes et routes techniques | lecture JSON | objets metier | comparer HTML et API |

## 3. Cartographie des risques a auditer

Les etudiants doivent identifier eux-memes les vulnerabilites precises. Les familles a couvrir pendant l'audit sont:

- controle d'acces et IDOR;
- authentification et actions sensibles;
- injection cote requetes, exports et journaux;
- XSS stockee ou reflechie;
- CSRF sur actions modifiant l'etat;
- upload et acces fichiers;
- SSRF et integrations sortantes;
- exposition excessive via API;
- secrets et configuration;
- logging et monitoring;
- mass assignment;
- design de workflows.

## 4. Couverture pedagogique

| Famille | Couverture estimee |
| --- | --- |
| Broken Access Control / IDOR | forte |
| Cryptographic Failures | moyenne |
| Injection | forte |
| Insecure Design | forte |
| Security Misconfiguration | forte |
| Vulnerable and Outdated Components | depend de `composer audit` |
| Identification and Authentication Failures | moyenne |
| Software and Data Integrity Failures | moyenne |
| Security Logging and Monitoring Failures | moyenne |
| Server-Side Request Forgery | forte |
| XSS | forte |
| CSRF | forte |
| Upload de fichiers | forte |
| API security | forte |
| Information disclosure | forte |
| Mass assignment | forte |

## 5. Estimation de charge etudiante

| Activite | Estimation |
| --- | ---: |
| Prise en main et installation | 1h |
| Cartographie fonctionnelle et technique | 3h |
| Cartographie roles/routes/API | 3h |
| Recherche et exploitation controlee | 6h |
| Documentation des preuves | 3h |
| Rapport de pentest | 3h |
| Plan de remediation | 2h |
| Cinq corrections detaillees | 2h |
| Preparation soutenance | 1h |

Total estime: **24h**.
