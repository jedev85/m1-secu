# Architecture

EventSecure Lab est une application Symfony 7 avec PHP 8.3, PostgreSQL, Doctrine ORM, Twig et le composant Security de Symfony.

## Modules

- Web: routes Symfony et templates Twig.
- API: endpoints JSON dans `ApiController`.
- Persistence: entites Doctrine `User`, `Event`, `Registration`, `Comment`, `Invoice`.
- Authentification: login formulaire, roles `ROLE_USER` et `ROLE_ADMIN`.
- Fichiers: avatars dans `public/uploads/avatars`, factures dans `var/invoices`.
- Native lab: exercices C independants dans `native-lab`.

## Modele metier

Le coeur metier est volontairement simple pour laisser de la place a l'audit:

- `User`: compte applicatif, roles, profil, note interne fictive.
- `Event`: evenement publiable avec lieu, prix, capacite et commentaires.
- `Registration`: inscription d'un utilisateur a un evenement.
- `Invoice`: facture fictive rattachee a un utilisateur.
- `Comment`: message persistant sur un evenement.

## Flux inscription

1. L'utilisateur consulte `/events`.
2. Il ouvre un evenement.
3. Il clique sur "S'inscrire".
4. L'application cree une `Registration`.
5. Une facture fictive est generee dans `var/invoices`.
6. Le detail evenement affiche que l'utilisateur est inscrit.
7. Le profil affiche la liste des inscriptions.
8. La page factures affiche la facture.

Ce flux donne du sens aux exercices: les factures et inscriptions ne sont pas des donnees abstraites, elles decoulent d'une action metier visible.

## Points d'observation

Les etudiants doivent analyser les controles d'acces, la validation des entrees, la configuration de securite, les logs et les choix de stockage.
