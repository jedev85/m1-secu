# Session 10 - Introduction fuzzing

## Objectifs pedagogiques
Comprendre l'approche par generation d'entrees et detection de crash.

## Contexte
`parser_fuzz_target.c` simule un parseur fragile.

## Notions abordees
Corpus, oracle de crash, sanitizers, minimisation.

## Parcours dans l'application
Compiler et executer le fuzz target sur des entrees locales.

## Exercices etudiants
N1: lire le parseur. N2: generer entrees simples. N3: trouver un crash. N4: corriger la limite. N5: fiche fuzzing.

## Indices progressifs
Tester longueurs et caracteres inattendus.

## Points de vigilance
Pas d'exploitation memoire avancee.

## Liens OWASP/CWE
CWE-20, CWE-787.

## Livrable attendu
Corpus minimal et correctif.
