# Laboratoire natif

Mini-lab C pour illustrer erreurs mémoire et fuzzing introductif. Il est indépendant de Symfony.

## Compilation

```bash
make
```

## AddressSanitizer

```bash
make asan
```

## Démonstration buffer

```bash
./buffer_overflow_demo texte_court
./buffer_overflow_demo entree_tres_tres_tres_longue
```

Objectif: observer un crash ou un rapport sanitizer, pas construire un exploit.

## Fuzzing simple

```bash
make fuzz-smoke
```

Le script génère quelques entrées locales pour `parser_fuzz_target`. Les étudiants peuvent enrichir le corpus et corriger le parseur.
