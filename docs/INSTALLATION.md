# Guide d'installation

Ce guide explique comment lancer EventSecure Lab en local avec Docker et `make`.

## Prerequis

Vous devez avoir:

- Docker Desktop ou Docker Engine avec Docker Compose;
- `make`;
- un terminal;
- une connexion reseau pour telecharger les images Docker et les dependances Composer au premier lancement.

Verifiez Docker:

```bash
docker --version
docker compose version
```

Verifiez `make`:

```bash
make --version
```

## Installer make

### macOS

Installez les Command Line Tools Apple:

```bash
xcode-select --install
```

Si vous utilisez Homebrew, vous pouvez aussi installer `make` avec:

```bash
brew install make
```

Sur macOS, la commande disponible par defaut s'appelle généralement `make`.

### Linux Debian/Ubuntu

```bash
sudo apt update
sudo apt install make
```

### Fedora

```bash
sudo dnf install make
```

### Windows

Utilisez de preference WSL2 avec Ubuntu, puis installez `make` dans le terminal WSL:

```bash
sudo apt update
sudo apt install make
```

Lancez ensuite les commandes du projet depuis le terminal WSL, dans le dossier du projet.

## Demarrage rapide

Depuis la racine du projet:

```bash
make up
make install
```

Puis ouvrez:

```text
http://localhost:8080
```

`make up` construit et demarre les containers Docker. `make install` installe les dependances PHP, prepare la base de donnees, applique les migrations et charge les donnees de demonstration.

## Comptes de test

Les fixtures creent notamment ces comptes:

```text
user1@example.test / password
user2@example.test / password
admin@example.test / password
```

Pour afficher les comptes dans le terminal:

```bash
make accounts
```

## Commandes utiles

```bash
make up
```

Construit et demarre les containers Docker en arriere-plan.

```bash
make install
```

Installe les dependances Composer, cree la base si besoin, lance les migrations et charge les fixtures.

```bash
make demo-data
```

Cree la base si besoin, lance les migrations, recharge les fixtures et affiche les comptes de test. Utilisez cette commande si les containers tournent deja et que vous voulez remettre les donnees de demonstration.

```bash
make reset
```

Supprime la base de donnees, la recree, relance les migrations et recharge les fixtures.

```bash
make fixtures
```

Recharge uniquement les fixtures dans une base deja disponible.

```bash
make test
```

Lance les tests PHPUnit dans le container PHP.

```bash
make logs
```

Affiche les logs Docker des services.

```bash
make shell
```

Ouvre un shell dans le container PHP.

```bash
make db
```

Ouvre une console PostgreSQL connectee a la base locale.

```bash
make down
```

Arrete les containers.

## Donnees de demonstration

Les donnees sont creees par les fixtures Symfony dans:

```text
src/DataFixtures/AppFixtures.php
```

Elles creent des utilisateurs, evenements, inscriptions, commentaires et factures fictives. Ces donnees servent uniquement au fonctionnement local de l'exercice.

## Probleme courant

Si l'application ne repond pas sur `http://localhost:8080`, verifiez:

```bash
docker compose ps
make logs
```

Si la base semble incoherente ou vide:

```bash
make reset
```
