# Session 10 - Introduction fuzzing

## Objectifs pédagogiques
Comprendre l'approche par génération d'entrées et détection de crash.

## Contexte
`parser_fuzz_target.c` simule un parseur fragile.

## Notions abordées
Corpus, oracle de crash, sanitizers, minimisation.

## Parcours dans l'application
Compiler et exécuter la cible de fuzzing sur des entrées locales.

## Exercices étudiants
N1: lire le parseur. N2: générer entrées simples. N3: trouver un crash. N4: corriger la limite. N5: fiche fuzzing.

## Indices progressifs
Tester longueurs et caractères inattendus.

## Points de vigilance
Pas d'exploitation mémoire avancée.

## Liens OWASP/CWE
CWE-20, CWE-787.

## Livrable attendu
Corpus minimal et correctif.
