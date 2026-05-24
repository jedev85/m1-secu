# Session 03 - XSS et CSRF

## Objectifs pedagogiques
Identifier une XSS stockee et une action sensible sans protection CSRF.

## Contexte
Les commentaires evenement sont affiches aux autres utilisateurs.

## Notions abordees
Echappement de sortie, contexte HTML, token CSRF, actions POST.

## Parcours dans l'application
Detail evenement, ajout et suppression de commentaire.

## Exercices etudiants
N1: reperer les sorties utilisateur. N2: provoquer une preuve visuelle locale. N3: supprimer `raw` et ajouter token CSRF. N4: tester la regression. N5: fiche XSS/CSRF.

## Indices progressifs
Inspecter Twig et les formulaires POST.

## Points de vigilance
Ne pas voler de cookie ni automatiser d'attaque.

## Liens OWASP/CWE
OWASP A03, A01. CWE-79, CWE-352.

## Livrable attendu
Deux fiches et deux corrections.
