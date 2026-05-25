# EventSecure Lab

Application Symfony locale pour l’exercice M1 cybersécurité de rapport JSON sur API.

## Avertissements

- Ne jamais deployer cette application en production.
- Usage strictement pédagogique, local et encadre.
- Executer dans un environnement isole.
- Les données, comptes, factures et secrets sont fictifs.
- Le périmètre d’audit est limité aux 5 endpoints listés dans la consigne.

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

Parcours métier conseille pour comprendre le lab:

1. Se connecter avec `user1@example.test`.
2. Ouvrir un événement depuis `/events`.
3. S’inscrire: le détail événement affiche le statut d'inscription.
4. Aller dans `/profile`: l'inscription apparaît dans "Mes inscriptions".
5. Aller dans `/invoices`: une facture fictive est disponible.
6. Publier un commentaire pour vérifier le parcours métier.

Endpoints API principaux:

- `GET /api/events`
- `GET /api/users/{id}`
- `GET /api/invoices/{id}`
- `POST /api/profile`
- `POST /api/preview-url`

Consigne du rapport JSON:

- `docs/rapport-vulnerabilites-etudiant.md`
- `docs/rapport-vulnerabilites-etudiant.pdf`

## Organisation

- `src/`: application Symfony
- `templates/`: vues Twig
- `docs/`: consigne du rapport JSON
- `docker/`: PHP-FPM et Nginx

## Limites connues

Le projet a ete scaffold sans accès reseau Packagist dans l'environnement de creation. `composer install` doit être lance dans un environnement ayant accès a Packagist, ou via cache Composer interne.
