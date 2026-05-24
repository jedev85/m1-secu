# Session 02 - SQL Injection

## Objectifs pedagogiques
Comprendre l'injection SQL par concatenation et corriger avec des requetes parametrees.

## Contexte
La recherche evenement utilise une implementation fragile.

## Notions abordees
Requete parametree, Doctrine DBAL, ORM, validation, logs de requetes.

## Parcours dans l'application
Page `/events`, parametre `q`, repository des evenements.

## Exercices etudiants
N1: localiser la recherche. N2: demontrer un comportement anormal local. N3: remplacer par un parametre lie. N4: ajouter tests et limites de recherche. N5: fiche SQLi.

## Indices progressifs
Comparer `findPublished()` et la recherche avec `q`.

## Points de vigilance
Pas d'exfiltration avancee, pas de payload destructeur.

## Liens OWASP/CWE
OWASP A03 Injection, CWE-89.

## Livrable attendu
Correctif, test, justification.
