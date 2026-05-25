# Notation enseignant des rapports JSON

La commande `app:grade-vulnerability-report` note un rapport étudiant sur 100 à partir d’un barème privé.

Le barème ne doit pas être commité dans le dépôt étudiant. Par défaut, la commande le cherche dans:

```bash
.teacher/vulnerability-answer-key.json
```

Ce dossier est ignoré par Git.

## Commande

```bash
php bin/console app:grade-vulnerability-report rapport-vulnerabilites.json
```

Avec un barème explicite:

```bash
php bin/console app:grade-vulnerability-report rapport-vulnerabilites.json --answer-key=/chemin/prive/bareme.json
```

Sortie JSON pour automatisation:

```bash
php bin/console app:grade-vulnerability-report rapport-vulnerabilites.json --json
```

## Format du barème

Chaque cas attendu indique les endpoints acceptés, les catégories acceptées, les sévérités acceptées et des termes qui doivent apparaître dans le finding. Les exemples ci-dessous sont fictifs et ne décrivent pas l’exercice réel.

```json
{
  "max_score": 100,
  "cases": [
    {
      "id": "case-01",
      "points": 20,
      "endpoints": ["GET /api/example"],
      "categories": ["example_category"],
      "severities": ["high", "critical"],
      "terms": ["mot attendu", "autre formulation attendue"],
      "evidence_terms": ["preuve attendue"]
    }
  ]
}
```

Les points des cas sont normalisés sur `max_score`. Par exemple, si les cas totalisent 85 points et que `max_score` vaut 100, un étudiant qui obtient 42,5 points bruts aura 50/100.

## Critères appliqués par cas

- endpoint: obligatoire; aucun point n’est accordé au cas si l’endpoint ne correspond pas.
- catégorie: 20% des points du cas.
- sévérité: 10% des points du cas.
- termes descriptifs: 30% des points du cas.
- termes de preuve: 20% des points du cas.
- présence de l’endpoint: 20% des points du cas.

Un cas est considéré reconnu si le score partiel atteint au moins 55% des points du cas.
