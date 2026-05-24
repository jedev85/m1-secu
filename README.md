# EventSecure Lab

Application Symfony volontairement vulnerable pour le module M1 cybersecurite "Vulnerabilites web et logiciel".

## Avertissements

- Ne jamais deployer cette application en production.
- Usage strictement pedagogique, local et encadre.
- Executer dans un environnement isole.
- Les donnees, comptes, factures et secrets sont fictifs.
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
- `make fixtures`: recharge les donnees
- `make test`: lance les tests
- `make logs`: suit les logs Docker
- `make shell`: ouvre un shell PHP
- `make db`: ouvre `psql`
- `make audit`: lance `composer audit`

## Surface fonctionnelle

L'application gere utilisateurs, evenements, inscriptions, commentaires, factures, upload d'avatar, admin basique, API JSON et previsualisation d'URL.

Parcours metier conseille pour comprendre le lab:

1. Se connecter avec `user1@example.test`.
2. Ouvrir un evenement depuis `/events`.
3. S'inscrire: le detail evenement affiche le statut d'inscription.
4. Aller dans `/profile`: l'inscription apparait dans "Mes inscriptions".
5. Aller dans `/invoices`: une facture fictive alimente les exercices IDOR/API.
6. Publier un commentaire pour les exercices XSS/CSRF.

Endpoints API principaux:

- `GET /api/events`
- `GET /api/events/{id}`
- `GET /api/users/{id}`
- `GET /api/invoices/{id}`
- `POST /api/profile`
- `POST /api/preview-url`

## Branches pedagogiques

- `student`: branche etudiants avec consignes et vulnerabilites.
- `teacher`: branche formateur avec guides, corrections, grilles et exemples de remediation.

## Organisation

- `src/`: application Symfony
- `templates/`: vues Twig
- `docs/`: deroule pedagogique et sujets
- `native-lab/`: mini-lab C pour memoire et fuzzing
- `docker/`: PHP-FPM et Nginx

## Limites connues

Le projet a ete scaffold sans acces reseau Packagist dans l'environnement de creation. `composer install` doit etre lance dans un environnement ayant acces a Packagist, ou via cache Composer interne.
