# Session 08 - Chaîne d'approvisionnement logicielle et CVE

## Objectifs pédagogiques
Comprendre l'audit de dépendances Composer et la qualification d'une CVE.

## Contexte
Le projet utilise des dépendances Symfony classiques.

## Notions abordées
Dépendances directes/transitives, advisories, versioning, exceptions.

## Parcours dans l'application
`composer.json`, `composer.lock` après installation, `docs/supply-chain.md`.

## Exercices étudiants
N1: lister dépendances. N2: lancer audit. N3: qualifier une alerte. N4: proposer un plan de mise à jour. N5: fiche supply chain.

## Indices progressifs
Comparer risque théorique et exploitabilité dans le contexte.

## Points de vigilance
Ne pas ajouter de dépendance dangereuse inutile.

## Liens OWASP/CWE
OWASP A06 Vulnerable and Outdated Components.

## Livrable attendu
Note de qualification CVE.
