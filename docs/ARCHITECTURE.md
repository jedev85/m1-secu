# Architecture

EventSecure Lab est une application Symfony 7 avec PHP 8.3, PostgreSQL, Doctrine ORM, Twig et le composant Security de Symfony.

## Modules

- Web: routes Symfony et templates Twig.
- API: endpoints JSON dans `ApiController`.
- Persistence: entités Doctrine `User`, `Event`, `Registration`, `Comment`, `Invoice`.
- Authentification: login formulaire, rôles `ROLE_USER` et `ROLE_ADMIN`.
- Fichiers: avatars dans `public/uploads/avatars`, factures dans `var/invoices`.
- Native lab: exercices C indépendants dans `native-lab`.

## Points d'observation

Les étudiants doivent analyser les contrôles d'accès, la validation des entrées, la configuration de sécurité, les logs et les choix de stockage.
