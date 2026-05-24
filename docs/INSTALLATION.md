# Installation

## Prerequis

- Docker et Docker Compose
- Make
- Acces reseau vers Packagist pour le premier `composer install`

## Procedure

```bash
make up
make install
```

Ouvrir http://localhost:8080.

## Reinitialisation

```bash
make reset
```

## Depannage

- Si le port `8080` est occupe, modifier `docker-compose.yml`.
- Si Composer echoue, verifier l'acces a `repo.packagist.org`.
- Les factures de demo sont regenerees par les fixtures dans `var/invoices`.
