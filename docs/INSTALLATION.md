# Installation

## Prérequis

- Docker et Docker Compose
- Make
- Accès réseau vers Packagist pour le premier `composer install`

## Procédure

```bash
make up
make install
```

Ouvrir http://localhost:8080.

## Réinitialisation

```bash
make reset
```

## Dépannage

- Si le port `8080` est occupé, modifier `docker-compose.yml`.
- Si Composer échoue, vérifier l'accès à `repo.packagist.org`.
- Les factures de démo sont régénérées par les fixtures dans `var/invoices`.
