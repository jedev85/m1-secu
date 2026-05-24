# Architecture

EventSecure Lab est une application Symfony 7 avec PHP 8.3, PostgreSQL, Doctrine ORM, Twig et Symfony Security.

## Modules

- Web: routes Symfony et templates Twig.
- API: endpoints JSON dans `ApiController`.
- Persistence: entites Doctrine `User`, `Event`, `Registration`, `Comment`, `Invoice`.
- Authentification: login formulaire, roles `ROLE_USER` et `ROLE_ADMIN`.
- Fichiers: avatars dans `public/uploads/avatars`, factures dans `var/invoices`.
- Native lab: exercices C independants dans `native-lab`.

## Points d'observation

Les etudiants doivent analyser les controles d'acces, la validation des entrees, la configuration de securite, les logs et les choix de stockage.
