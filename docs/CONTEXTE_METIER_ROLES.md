# Contexte métier et droits attendus

Ce document décrit la politique d'accès **théorique** de la PME. Il sert de
référentiel pour l'audit. Il ne décrit pas nécessairement le comportement réel
de l'application.

Un écart entre cette politique et le comportement observé doit être analysé,
prouvé et qualifié. Tout écart n'est pas automatiquement critique : il faut
prendre en compte les données exposées, l'action possible et le contexte métier.

## Principes généraux

- Une personne ne consulte que les données nécessaires à son activité.
- Le propriétaire d'un objet peut le gérer dans les limites de son rôle.
- Être membre d'un projet donne accès au projet, mais pas aux fonctions
  d'administration de la plateforme.
- Une donnée privée reste limitée à son propriétaire et aux rôles explicitement
  autorisés.
- Une donnée confidentielle est réservée aux membres concernés et au management.
- Les actions sensibles doivent être autorisées côté serveur, quelle que soit
  leur visibilité dans l'interface.
- Une action modifiant ou supprimant une donnée doit être intentionnelle,
  traçable et protégée contre une requête forcée.
- Les API appliquent les mêmes règles métier que les pages HTML.

## Utilisateur non authentifié

Un visiteur non connecté peut uniquement :

- afficher la page de connexion ;
- soumettre ses identifiants ;
- accéder aux ressources statiques nécessaires à cette page.

Il ne doit accéder à aucune donnée métier, API interne, configuration, journal,
document ou fonction d'administration.

## ROLE_USER - Collaborateur

Le collaborateur gère son portefeuille et participe aux projets auxquels il est
affecté.

Il peut normalement :

- consulter et modifier son propre profil ;
- gérer les clients dont il est propriétaire ;
- créer des tickets et suivre ceux qui concernent son activité ;
- consulter ses factures ou celles de son portefeuille ;
- consulter les projets dont il est propriétaire ou membre ;
- consulter et mettre à jour les tâches qui lui sont assignées ;
- créer des notes et consulter celles dont la visibilité l'autorise ;
- envoyer, lire et supprimer ses propres messages ;
- déposer et consulter les documents auxquels il a droit.

Il ne doit pas :

- gérer les rôles ou les comptes d'autres personnes ;
- consulter les messages privés d'autres utilisateurs ;
- accéder aux secrets, journaux techniques ou paramètres sensibles ;
- modifier un projet, une tâche, un client ou une facture sans lien métier ;
- accéder à un projet confidentiel dont il n'est ni propriétaire ni membre.

## ROLE_SUPPORT - Équipe support

Le support traite les demandes et incidents clients. Son accès transversal doit
rester limité aux informations nécessaires au diagnostic.

Il peut normalement :

- consulter les tickets de support ;
- commenter, assigner et faire évoluer les tickets ;
- consulter les informations client utiles au traitement d'un ticket ;
- consulter les documents techniques partagés avec l'équipe support ;
- participer aux projets et tâches auxquels il est explicitement affecté.

Il ne doit pas :

- consulter les factures, budgets ou notes confidentielles sans justification ;
- modifier la propriété d'un client ou d'un projet ;
- administrer les utilisateurs, rôles, secrets ou paramètres applicatifs ;
- consulter les messages privés sans en être expéditeur ou destinataire.

## ROLE_MANAGER - Responsable métier

Le manager pilote l'activité, les équipes, les projets et les données
commerciales de son périmètre.

Il peut normalement :

- consulter les clients, tickets, factures et projets de son périmètre ;
- créer et piloter les projets ;
- gérer les membres, budgets, statuts et tâches des projets ;
- consulter les informations marquées pour le management ;
- utiliser les fonctions d'import et d'export métier ;
- configurer et tester les intégrations métier autorisées ;
- consulter les journaux d'activité utiles au pilotage.

Il ne doit pas :

- s'attribuer ou attribuer des rôles techniques ;
- modifier les secrets de sécurité ou la configuration système ;
- consulter les mots de passe, jetons techniques ou informations de session ;
- supprimer ou modifier des données hors de son périmètre sans justification.

## ROLE_ADMIN - Administrateur technique

L'administrateur maintient la plateforme. Son rôle n'implique pas
automatiquement un besoin métier sur toutes les données.

Il peut normalement :

- gérer les comptes et les rôles ;
- administrer les paramètres techniques ;
- consulter les journaux techniques et de sécurité ;
- diagnostiquer la configuration et les intégrations ;
- intervenir sur les données métier lorsqu'une opération de support le justifie
  et qu'elle est tracée.

Il ne doit pas :

- utiliser ses privilèges pour consulter des données métier sans justification ;
- exposer les secrets ou les journaux à des rôles non autorisés ;
- effectuer une action sensible sans trace ;
- contourner les règles de confidentialité en dehors d'une intervention
  technique légitime.

## Règles par type de donnée

| Donnée | Accès attendu |
| --- | --- |
| Profil utilisateur | utilisateur concerné ; administration pour la gestion du compte |
| Client | propriétaire ; support si nécessaire à un ticket ; manager du périmètre |
| Ticket | créateur/client concerné ; support assigné ; manager du périmètre |
| Facture | propriétaire du portefeuille ; manager concerné |
| Projet standard | propriétaire, membres, manager concerné |
| Projet confidentiel | propriétaire, membres explicites, management autorisé |
| Tâche | créateur, personne assignée, responsables du projet |
| Note privée | auteur uniquement, sauf règle métier explicite |
| Note d'équipe | membres de l'équipe ou du projet concerné |
| Message | expéditeur et destinataire |
| Document privé | déposant et personnes explicitement autorisées |
| Journal métier | manager concerné et administrateur justifié |
| Journal technique | administrateur technique |
| Paramètre sensible | administrateur technique uniquement |
| Webhook et secret | responsables autorisés de l'intégration et administrateur |

## Qualification d'un écart

Pour chaque comportement suspect, documentez :

1. le rôle et le compte utilisés ;
2. la ressource ciblée et son propriétaire ;
3. le droit attendu selon ce document ;
4. le comportement réel observé ;
5. les données lues ou les actions réalisées ;
6. l'impact métier ;
7. la correction recommandée.

En cas d'ambiguïté métier, formulez explicitement votre hypothèse dans le
rapport au lieu de conclure sans justification.
