# Session 05 - Auth et Access Control

## Objectifs pédagogiques
Analyser rôles, propriétaire de ressource, IDOR et routes admin.

## Contexte
Factures, API utilisateurs et rapports admin exposent des erreurs de contrôle.

## Notions abordées
RBAC, ABAC, voters Symfony, IDOR, least privilege.

## Parcours dans l'application
`/invoices`, `/invoices/{id}/download`, `/api/users/{id}`, `/admin/reports`.

## Exercices étudiants
N1: lister les ressources protégées. N2: tester un autre identifiant local. N3: ajouter contrôles propriétaire. N4: créer voter ou policy. N5: fiche access control.

## Indices progressifs
Changer uniquement des IDs de fixtures.

## Points de vigilance
Ne pas utiliser de comptes ou données externes.

## Liens OWASP/CWE
OWASP A01, CWE-639, CWE-862.

## Livrable attendu
Matrice rôle/ressource/action et correctifs.
