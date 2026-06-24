# AuditLab Symfony - Plateforme de gestion interne

Application Symfony volontairement vulnerable pour un TP d'audit securite web en M1 Expert Cybersecurite.

Le contexte simule une PME qui utilise une plateforme interne pour gerer ses utilisateurs, clients, tickets support, documents, factures, projets internes, taches, notes, messages, webhooks, exports CSV, parametres applicatifs, imports et espace admin.

## Cadre d'utilisation

Cette application est un support pedagogique local. Elle ne doit jamais etre exposee sur Internet ni utilisee contre une cible reelle. Les tests doivent rester limites a votre machine ou a un environnement de TP explicitement autorise par l'enseignant.

## Installation avec Docker

Prerequis: Docker Desktop avec Docker Compose.

```bash
docker compose up --build
```

L'application est ensuite disponible sur `http://localhost:8000`.

Pour repartir avec une base MySQL vide:

```bash
docker compose down -v
docker compose up --build
```

## Installation sans Docker

Prerequis: PHP 8.2+, extension `pdo_mysql`, Composer, Symfony CLI et MySQL 8.4.

Creer une base `auditlab` et un utilisateur MySQL correspondant a la variable `DATABASE_URL` de `.env`, puis lancer:

```bash
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```

MySQL 8.4 est utilise par defaut.

Commandes utiles:

```bash
make install
make reset-db
make test
make audit
make serve
```

## Comptes de demonstration

Tous les comptes utilisent le mot de passe `password`.

| Email | Role principal |
| --- | --- |
| user1@example.com | ROLE_USER |
| user2@example.com | ROLE_USER |
| user3@example.com | ROLE_USER |
| user4@example.com | ROLE_USER |
| user5@example.com | ROLE_USER |
| support@example.com | ROLE_SUPPORT |
| support2@example.com | ROLE_SUPPORT |
| manager@example.com | ROLE_MANAGER |
| admin@example.com | ROLE_ADMIN |

## Perimetre autorise

Vous pouvez auditer toutes les routes locales de l'application, les endpoints API JSON, les exports, les imports depuis URL, les uploads et la configuration Symfony fournie dans ce depot.

Vous ne devez pas attaquer de services tiers, scanner Internet, voler de vrais identifiants, deposer de code destructeur, utiliser de reverse shell ou tenter de persistance systeme.

## Livrables attendus

### 1. Methodologie d'audit

Expliquez votre reconnaissance fonctionnelle, la cartographie des routes, l'identification des roles, l'analyse des surfaces d'attaque, les tests manuels, les tests automatises eventuels, la priorisation et les limites de l'audit.

### 2. Documentation technique

Documentez l'architecture, les entites principales, les routes importantes, les roles et permissions, les flux sensibles et les zones a risque.

### 3. Rapport de pentest

Format attendu:

- synthese executive;
- perimetre;
- methodologie;
- tableau recapitulatif des vulnerabilites;
- detail des vulnerabilites;
- preuves d'exploitation;
- impact;
- niveau de risque;
- recommandations;
- conclusion.

Pour chaque vulnerabilite: identifiant, titre, criticite, famille OWASP, description, prerequis, etapes de reproduction, preuve, impact et correction recommandee.

### 4. Plan de remediation

Fournissez un plan priorise:

- remediations critiques sous 48h;
- remediations court terme sous 2 semaines;
- remediations moyen terme sous 1 a 2 mois;
- durcissement long terme;
- recommandations organisationnelles.

### 5. Cinq exemples de correction

Pour au moins cinq corrections concretes, fournissez la vulnerabilite ciblee, un extrait de code vulnerable, l'explication du probleme, un extrait de code corrige, la justification, un test de verification et le risque residuel eventuel.

## Points d'entree

Commencez par les menus de l'application apres connexion, puis cartographiez vous-meme les routes HTML, API et flux techniques disponibles localement. Les endpoints non visibles dans l'interface font partie du perimetre d'audit s'ils appartiennent a cette application.

Documents utiles:

- `docs/CONSIGNE_ETUDIANTS.md`
- `docs/CHECKLIST_AUDIT.md`
- `docs/TEMPLATE_RAPPORT.md`
- `docs/TEMPLATE_REMEDIATION.md`
- `docs/PLAN_COURS_24H.md`
- `docs/GRILLE_EVALUATION.md`
- `docs/http-requests.http`

## Remise

Le rendu doit etre professionnel, structure et reproductible. Les preuves doivent etre suffisantes pour convaincre un responsable technique sans causer de dommage a l'environnement local.
