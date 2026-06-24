# Contexte metier et droits attendus

Ce document decrit la politique d'acces **theorique** de la PME. Il sert de
referentiel pour l'audit. Il ne decrit pas necessairement le comportement reel
de l'application.

Un ecart entre cette politique et le comportement observe doit etre analyse,
prouve et qualifie. Tout ecart n'est pas automatiquement critique: il faut
prendre en compte les donnees exposees, l'action possible et le contexte metier.

## Principes generaux

- Une personne ne consulte que les donnees necessaires a son activite.
- Le proprietaire d'un objet peut le gerer dans les limites de son role.
- Etre membre d'un projet donne acces au projet, mais pas aux fonctions
  d'administration de la plateforme.
- Une donnee privee reste limitee a son proprietaire et aux roles explicitement
  autorises.
- Une donnee confidentielle est reservee aux membres concernes et au management.
- Les actions sensibles doivent etre autorisees cote serveur, quelle que soit
  leur visibilite dans l'interface.
- Une action modifiant ou supprimant une donnee doit etre intentionnelle,
  tracable et protegee contre une requete forcee.
- Les API appliquent les memes regles metier que les pages HTML.

## Utilisateur non authentifie

Un visiteur non connecte peut uniquement:

- afficher la page de connexion;
- soumettre ses identifiants;
- acceder aux ressources statiques necessaires a cette page.

Il ne doit acceder a aucune donnee metier, API interne, configuration, journal,
document ou fonction d'administration.

## ROLE_USER - Collaborateur

Le collaborateur gere son portefeuille et participe aux projets auxquels il est
affecte.

Il peut normalement:

- consulter et modifier son propre profil;
- gerer les clients dont il est proprietaire;
- creer des tickets et suivre ceux qui concernent son activite;
- consulter ses factures ou celles de son portefeuille;
- consulter les projets dont il est proprietaire ou membre;
- consulter et mettre a jour les taches qui lui sont assignees;
- creer des notes et consulter celles dont la visibilite l'autorise;
- envoyer, lire et supprimer ses propres messages;
- deposer et consulter les documents auxquels il a droit.

Il ne doit pas:

- gerer les roles ou les comptes d'autres personnes;
- consulter les messages prives d'autres utilisateurs;
- acceder aux secrets, journaux techniques ou parametres sensibles;
- modifier un projet, une tache, un client ou une facture sans lien metier;
- acceder a un projet confidentiel dont il n'est ni proprietaire ni membre.

## ROLE_SUPPORT - Equipe support

Le support traite les demandes et incidents clients. Son acces transversal doit
rester limite aux informations necessaires au diagnostic.

Il peut normalement:

- consulter les tickets de support;
- commenter, assigner et faire evoluer les tickets;
- consulter les informations client utiles au traitement d'un ticket;
- consulter les documents techniques partages avec l'equipe support;
- participer aux projets et taches auxquels il est explicitement affecte.

Il ne doit pas:

- consulter les factures, budgets ou notes confidentielles sans justification;
- modifier la propriete d'un client ou d'un projet;
- administrer les utilisateurs, roles, secrets ou parametres applicatifs;
- consulter les messages prives sans en etre expediteur ou destinataire.

## ROLE_MANAGER - Responsable metier

Le manager pilote l'activite, les equipes, les projets et les donnees
commerciales de son perimetre.

Il peut normalement:

- consulter les clients, tickets, factures et projets de son perimetre;
- creer et piloter les projets;
- gerer les membres, budgets, statuts et taches des projets;
- consulter les informations marquees pour le management;
- utiliser les fonctions d'import et d'export metier;
- configurer et tester les integrations metier autorisees;
- consulter les journaux d'activite utiles au pilotage.

Il ne doit pas:

- s'attribuer ou attribuer des roles techniques;
- modifier les secrets de securite ou la configuration systeme;
- consulter les mots de passe, jetons techniques ou informations de session;
- supprimer ou modifier des donnees hors de son perimetre sans justification.

## ROLE_ADMIN - Administrateur technique

L'administrateur maintient la plateforme. Son role n'implique pas
automatiquement un besoin metier sur toutes les donnees.

Il peut normalement:

- gerer les comptes et les roles;
- administrer les parametres techniques;
- consulter les journaux techniques et de securite;
- diagnostiquer la configuration et les integrations;
- intervenir sur les donnees metier lorsqu'une operation de support le justifie
  et qu'elle est tracee.

Il ne doit pas:

- utiliser ses privileges pour consulter des donnees metier sans justification;
- exposer les secrets ou les journaux a des roles non autorises;
- effectuer une action sensible sans trace;
- contourner les regles de confidentialite en dehors d'une intervention
  technique legitime.

## Regles par type de donnee

| Donnee | Acces attendu |
| --- | --- |
| Profil utilisateur | utilisateur concerne; administration pour gestion du compte |
| Client | proprietaire; support si necessaire a un ticket; manager du perimetre |
| Ticket | createur/client concerne; support assigne; manager du perimetre |
| Facture | proprietaire du portefeuille; manager concerne |
| Projet standard | proprietaire, membres, manager concerne |
| Projet confidentiel | proprietaire, membres explicites, management autorise |
| Tache | createur, personne assignee, responsables du projet |
| Note privee | auteur uniquement, sauf regle metier explicite |
| Note equipe | membres de l'equipe ou du projet concerne |
| Message | expediteur et destinataire |
| Document prive | deposant et personnes explicitement autorisees |
| Journal metier | manager concerne et administrateur justifie |
| Journal technique | administrateur technique |
| Parametre sensible | administrateur technique uniquement |
| Webhook et secret | responsables autorises de l'integration et administrateur |

## Qualification d'un ecart

Pour chaque comportement suspect, documentez:

1. le role et le compte utilises;
2. la ressource ciblee et son proprietaire;
3. le droit attendu selon ce document;
4. le comportement reel observe;
5. les donnees lues ou les actions realisees;
6. l'impact metier;
7. la correction recommandee.

En cas d'ambiguite metier, formulez explicitement votre hypothese dans le
rapport au lieu de conclure sans justification.
