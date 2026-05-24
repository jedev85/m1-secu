# Session 05 - Auth et Access Control

## Objectifs pedagogiques
Analyser roles, proprietaire de ressource, IDOR et routes admin.

## Contexte
Factures, API utilisateurs et rapports admin exposent des erreurs de controle.

## Notions abordees
RBAC, ABAC, voters Symfony, IDOR, least privilege.

## Parcours dans l'application
`/invoices`, `/invoices/{id}/download`, `/api/users/{id}`, `/admin/reports`.

## Exercices etudiants
N1: lister les ressources protegees. N2: tester un autre identifiant local. N3: ajouter controles proprietaire. N4: creer voter ou policy. N5: fiche access control.

## Indices progressifs
Changer uniquement des IDs de fixtures.

## Points de vigilance
Ne pas utiliser de comptes ou donnees externes.

## Liens OWASP/CWE
OWASP A01, CWE-639, CWE-862.

## Livrable attendu
Matrice role/ressource/action et correctifs.
