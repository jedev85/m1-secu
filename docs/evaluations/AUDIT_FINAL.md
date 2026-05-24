# Audit final

## Consigne etudiant

Auditer EventSecure Lab en boite grise avec acces au code, a l'application locale et aux fixtures.

## Livrables attendus

- Rapport d'audit applicatif
- Tableau des vulnerabilites
- Trois corrections proposees avec verification
- Annexe methodologique

## Perimetre minimal

Le rapport doit couvrir au moins:

- un flux web authentifie;
- un endpoint API;
- le flux inscription -> facture;
- une interaction navigateur liee aux commentaires;
- une configuration ou pratique d'exploitation locale;
- un element de supply chain ou de logs.

## Contraintes

- Tests uniquement sur l'instance locale.
- Donnees fictives uniquement.
- Pas de payload destructeur.
- Les preuves doivent etre courtes et reproductibles.
- Les corrections proposees doivent citer les fichiers concernes.

## Structure attendue du rapport

1. Synthese executive: 10 a 15 lignes.
2. Perimetre et methode.
3. Tableau de priorisation.
4. Constats detailles.
5. Remediations et tests proposes.
6. Limites de l'audit.
7. Annexes techniques.

## Format d'un constat

```text
Titre:
Criticite:
Route/fichier:
Acteur concerne:
Description:
Preuve locale:
Impact metier:
Cause racine:
Remediation:
Verification:
```

## Attendus quantitatifs

- 5 a 8 constats priorises.
- Au moins 2 preuves web.
- Au moins 1 preuve API.
- Au moins 1 correction avec test ou procedure de verification.
- Au moins 1 risque non technique explique cote metier.

## Bareme indicatif

- Methodologie: 20 %
- Identification et qualification: 30 %
- Preuves controlees: 20 %
- Remediation: 20 %
- Qualite de redaction: 10 %

La grille detaillee et la correction sont reservees a la branche `teacher`.
