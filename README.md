# EventSecure Lab

Application Symfony volontairement vulnérable pour le module M1 cybersécurité "Vulnérabilités web et logiciel".

## Avertissements

- Ne jamais déployer cette application en production.
- Usage strictement pédagogique, local et encadré.
- Exécuter dans un environnement isolé.
- Les données, comptes, factures et secrets sont fictifs.
- Plusieurs mauvaises pratiques sont intentionnelles afin de servir d'exercices.

## Démarrage

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

- `make up`: construit et démarre les containers
- `make down`: arrête les containers
- `make install`: installe Composer, migre et charge les fixtures
- `make reset`: reconstruit la base et recharge les fixtures
- `make fixtures`: recharge les données
- `make test`: lance les tests
- `make logs`: suit les logs Docker
- `make shell`: ouvre un shell PHP
- `make db`: ouvre `psql`
- `make audit`: lance `composer audit`

## Surface fonctionnelle

L'application gère utilisateurs, événements, inscriptions, commentaires, factures, upload d'avatar, admin basique, API JSON et prévisualisation d'URL.

Les pages evenement sont aussi des pages d'atelier. Chaque evenement affiche:

- le scenario metier de la faille;
- les zones de test;
- les notions travaillees;
- un parcours N1 a N5;
- les livrables;
- les criteres de verification;
- les points de vigilance.

Parcours metier conseille pour comprendre le lab:

1. Se connecter avec `user1@example.test`.
2. Ouvrir un evenement depuis `/events`.
3. Lire le bloc "Atelier associe": il indique la zone de test liee au theme de l evenement.
4. S'inscrire: le detail evenement affiche le statut d'inscription.
5. Aller dans `/profile`: l'inscription apparait dans "Mes inscriptions".
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

- `student`: branche étudiants avec consignes et vulnérabilités.
- `teacher`: branche formateur avec guides, corrections, grilles et exemples de remédiation.

## Organisation

- `src/`: application Symfony
- `templates/`: vues Twig
- `docs/`: déroulé pédagogique et sujets
- `native-lab/`: mini-lab C pour mémoire et fuzzing
- `docker/`: PHP-FPM et Nginx

## Limites connues

Le projet à été scaffold sans accès réseau Packagist dans l'environnement de création. `composer install` doit être lancé dans un environnement ayant accès à Packagist, ou vià cache Composer interne.
