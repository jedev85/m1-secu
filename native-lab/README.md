# Laboratoire natif

Mini-lab C pour illustrer erreurs memoire et fuzzing introductif. Il est independant de Symfony.

## Compilation

```bash
make
```

## AddressSanitizer

```bash
make asan
```

## Demonstration buffer

```bash
./buffer_overflow_demo texte_court
./buffer_overflow_demo entree_tres_tres_tres_longue
```

Objectif: observer un crash ou un rapport sanitizer, pas construire un exploit.

## Fuzzing simple

```bash
make fuzz-smoke
```

Le script genere quelques entrees locales pour `parser_fuzz_target`. Les etudiants peuvent enrichir le corpus et corriger le parseur.
