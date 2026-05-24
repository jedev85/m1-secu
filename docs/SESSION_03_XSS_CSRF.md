# Session 03 - XSS et CSRF

## Objectifs pédagogiques
Identifier une XSS stockée et une action sensible sans protection CSRF.

## Contexte
Les commentaires événement sont affichés aux autres utilisateurs.

## Notions abordées
Échappement de sortie, contexte HTML, token CSRF, actions POST.

## Parcours dans l'application
Détail événement, ajout et suppression de commentaire.

## Exercices étudiants
N1: repérer les sorties utilisateur. N2: provoquer une preuve visuelle locale. N3: supprimer `raw` et ajouter token CSRF. N4: tester la régression. N5: fiche XSS/CSRF.

## Indices progressifs
Inspecter Twig et les formulaires POST.

## Points de vigilance
Ne pas voler de cookie ni automatiser d'attaque.

## Liens OWASP/CWE
OWASP A03, A01. CWE-79, CWE-352.

## Livrable attendu
Deux fiches et deux corrections.
