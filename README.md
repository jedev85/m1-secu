# EventSecure Lab

Application Symfony volontairement vulnerable pour le module M1 cybersécurité "Vulnerabilites web et logiciel".

## Avertissements

- Ne jamais deployer cette application en production.
- Usage strictement pédagogique, local et encadre.
- Executer dans un environnement isole.
- Les données, comptes, factures et secrets sont fictifs.
- Plusieurs mauvaises pratiques sont intentionnelles afin de servir d'exercices.

## Demarrage

```bash
make up
make install
```

Application: http://localhost:8080

Comptes de test:

- `user1@example.test` / `password`
- `user2@example.test` / `password`
- `admin@example.test` / `password`

## Commandes

- `make up`: construit et demarre les containers
- `make down`: arrete les containers
- `make install`: installe Composer, migre et charge les fixtures
- `make reset`: reconstruit la base et recharge les fixtures
- `make fixtures`: recharge les données
- `make test`: lance les tests
- `make logs`: suit les logs Docker
- `make shell`: ouvre un shell PHP
- `make db`: ouvre `psql`
- `make audit`: lance `composer audit`

## Surface fonctionnelle

L’application gere utilisateurs, événements, inscriptions, commentaires, factures, upload d'avatar, admin basique, API JSON et previsualisation d'URL.

Les pages événement sont aussi des pages d'atelier. Chaque événement affiche:

- le scénario métier de la faille;
- les zones de test;
- les notions travaillees;
- un parcours N1 a N5;
- les livrables;
- les critères de vérification;
- les points de vigilance.

Parcours métier conseille pour comprendre le lab:

1. Se connecter avec `user1@example.test`.
2. Ouvrir un événement depuis `/events`.
3. Lire le bloc "Atelier associé": il indique la zone de test liée au thème de l’événement.
4. S’inscrire: le détail’événement affiche le statut d'inscription.
5. Aller dans `/profile`: l'inscription apparaît dans "Mes inscriptions".
6. Aller dans `/invoices`: une facture fictive alimente les exercices IDOR/API.
7. Publier un commentaire pour les exercices XSS/CSRF.

Endpoints API principaux:

- `GET /api/events`
- `GET /api/events/{id}`
- `GET /api/users/{id}`
- `GET /api/invoices/{id}`
- `POST /api/profile`
- `POST /api/preview-url`

## Branches pédagogiques

- `student`: branche etudiants avec consignes et vulnérabilités.
- `teacher`: branche formateur avec guides, corrections, grilles et exemples de remédiation.

## Organisation

- `src/`: application Symfony
- `templates/`: vues Twig
- `docs/`: deroule pédagogique et sujets
- `native-lab/`: mini-lab C pour mémoire et fuzzing
- `docker/`: PHP-FPM et Nginx

## Limites connues

Le projet a ete scaffold sans accès reseau Packagist dans l'environnement de creation. `composer install` doit être lance dans un environnement ayant accès a Packagist, ou via cache Composer interne.
