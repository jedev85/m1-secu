# Session 02 - SQL Injection

## Objectifs pédagogiques
Comprendre l'injection SQL par concaténation et corriger avec des requêtes paramétrées.

## Contexte
La recherche événement utilise une implémentation fragile.

## Notions abordées
Requête paramétrée, Doctrine DBAL, ORM, validation, logs de requêtes.

## Parcours dans l'application
Page `/events`, paramètre `q`, repository des événements.

## Exercices étudiants
N1: localiser la recherche. N2: démontrer un comportement anormal local. N3: remplacer par un paramètre lié. N4: ajouter tests et limites de recherche. N5: fiche SQLi.

## Indices progressifs
Comparer `findPublished()` et la recherche avec `q`.

## Points de vigilance
Pas d'exfiltration avancée, pas de payload destructeur.

## Liens OWASP/CWE
OWASP A03 Injection, CWE-89.

## Livrable attendu
Correctif, test, justification.
