# Workbook etudiant

Ce workbook complete les fiches de session. Il decrit des parcours concrets dans EventSecure Lab, avec des exercices progressifs. Les etudiants travaillent uniquement sur une instance locale et sur des donnees fictives.

## Avant de commencer

Verifier que l'application demarre:

```bash
make up
make install
```

Comptes disponibles:

- `user1@example.test` / `password`
- `user2@example.test` / `password`
- `admin@example.test` / `password`

Parcours metier de base:

1. Se connecter avec `user1@example.test`.
2. Consulter `/events`.
3. Ouvrir un evenement et lire le bloc "Atelier associe".
4. S'inscrire.
5. Verifier le statut d'inscription sur l'evenement.
6. Aller dans le profil pour voir l'inscription.
7. Aller dans les factures pour voir la facture fictive.
8. Ajouter un commentaire.
9. Interroger quelques routes API.

Ce parcours sert de reference. Une faille est plus interessante quand on sait quel comportement metier elle detourne.

Chaque evenement est aussi une porte d'entree pedagogique. Un evenement nomme "SQL Injection" renvoie vers la recherche vulnerable, un evenement "XSS, CSRF" utilise les commentaires de sa propre page, un evenement "API et SSRF" renvoie vers les endpoints JSON et la preview URL. Certaines failles restent techniquement dans des fonctions transverses, mais la page evenement indique ou les tester.

## Session 01 - Cartographie OWASP

### Objectifs

Identifier les surfaces d'attaque avant de chercher une faille precise. Distinguer routes publiques, routes authentifiees, routes admin, API et fichiers servis publiquement.

### Travail en presentiel

- Lister les pages visibles sans compte.
- Lister les pages accessibles avec `ROLE_USER`.
- Lister les pages admin.
- Identifier les endpoints API dans les routes Symfony.
- Identifier les repertoires d'upload et de factures.
- Construire une matrice simple: fonctionnalite, donnees manipulees, acteurs, controles attendus.

### Exercices

- N1: produire une carte de navigation de l'application.
- N2: associer chaque fonctionnalite a une famille OWASP.
- N3: choisir trois zones prioritaires et justifier le choix.
- N4: proposer une strategie de tests fonctionnels securite.
- N5: rediger une note de cadrage d'audit en une page.

### FOAD

Relire OWASP Top 10 Web et API Security Top 10. Completer la matrice avec au moins cinq risques qui ne seront pas exploites immediatement.

## Session 02 - SQL Injection

### Objectifs

Comprendre pourquoi une recherche metier devient risquee quand elle concatene une entree utilisateur dans une requete SQL. Corriger sans casser le comportement attendu.

### Parcours

La page `/events` accepte un parametre de recherche. Le code passe par le repository des evenements. Les resultats sont affiches comme des tableaux quand la recherche est active.

### Exercices

- N1: localiser le controleur, le repository et le template impliques.
- N2: tester des entrees atypiques et observer les erreurs ou comportements anormaux.
- N3: remplacer la concatenation par une requete parametree.
- N4: ajouter une limite de longueur, un comportement vide clair et un test de non-regression.
- N5: rediger une fiche d'audit reliant cause racine, impact et remediation.

### Points d'attention

La preuve doit rester locale et non destructive. Le but n'est pas d'exfiltrer massivement les donnees fictives, mais de montrer que le controle de la requete echappe a l'application.

### FOAD

Comparer trois approches: DBAL parametre, QueryBuilder Doctrine, formulaire Symfony valide. Expliquer laquelle s'integre le mieux ici.

## Session 03 - XSS et CSRF

### Objectifs

Analyser les interactions navigateur: stockage de commentaires, rendu Twig, formulaires POST et actions sensibles.

### Parcours

Un utilisateur connecte publie un commentaire sur un evenement. Les autres utilisateurs consultent ensuite la meme page. Une suppression de commentaire existe aussi.

### Exercices

- N1: identifier les champs qui reviennent dans le HTML.
- N2: demontrer une execution ou alteration visuelle controlee dans le navigateur local.
- N3: corriger le rendu du commentaire en respectant le contexte HTML.
- N4: ajouter un token CSRF et une verification de proprietaire/admin sur la suppression.
- N5: rediger deux constats separes: XSS stockee et CSRF/action non autorisee.

### FOAD

Creer une courte checklist de revue Twig: echappement, `raw`, attributs, URL, JavaScript inline, fragments HTML autorises.

## Session 04 - SDLC et DevSecOps

### Objectifs

Mettre la securite dans le cycle de developpement: definition of done, tests, analyse statique, revue de dependances, secrets et deploiement local.

### Exercices

- N1: inventorier les commandes Makefile et leur role.
- N2: identifier ce qui manque pour une CI minimale.
- N3: ajouter ou decrire un test fonctionnel de securite simple.
- N4: proposer une definition of done securite pour ce projet.
- N5: rediger une page "security gate" pour merge request.

### FOAD

Rediger un exemple de checklist MR pour une correction de faille: preuves avant/apres, tests, impact utilisateur, migration necessaire.

## Session 05 - Authentification et controle d'acces

### Objectifs

Relier les roles Symfony, les objets metier et les autorisations. Comprendre pourquoi un simple `ROLE_USER` ne suffit pas pour proteger les factures ou les profils.

### Parcours

S'inscrire a un evenement cree une inscription visible dans le profil et une facture fictive. Les factures ont un proprietaire, mais certaines routes acceptent un identifiant direct.

### Exercices

- N1: comparer les factures visibles par deux comptes.
- N2: observer ce qui se passe quand un identifiant de facture change.
- N3: corriger l'acces aux factures par verification proprietaire ou role admin.
- N4: traiter le cas API et le cas telechargement fichier.
- N5: proposer un voter Symfony `INVOICE_VIEW` et le tester.

### FOAD

Rediger une matrice d'autorisation: utilisateur anonyme, utilisateur standard, proprietaire, admin. Inclure evenements, commentaires, factures, profil, API.

## Session 06 - API, BOLA et SSRF

### Objectifs

Auditer une API JSON et une fonctionnalite serveur qui recupere une URL fournie par l'utilisateur.

### Parcours

Les endpoints `/api/users/{id}` et `/api/invoices/{id}` exposent des donnees sensibles fictives. Le endpoint `/api/preview-url` contacte une URL distante pour en extraire un titre.

### Exercices

- N1: documenter les schemas de reponse reels.
- N2: identifier les champs excessifs et les objets accessibles par ID.
- N3: definir des DTO de sortie minimaux.
- N4: filtrer la preview URL: scheme, host, DNS, IP privees/locales, timeout, taille.
- N5: produire une fiche API avec impact metier et tests de correction.

### FOAD

Comparer OWASP API1, API3 et CWE-918. Donner un exemple de test automatisable pour chacun.

## Session 07 - Secrets, logs et hardening

### Objectifs

Distinguer secret reel, faux secret pedagogique, configuration locale et information sensible dans les logs.

### Exercices

- N1: lire `.env`, `docs/bad-practices.md` et la configuration Monolog.
- N2: localiser un log trop bavard.
- N3: proposer une redaction des logs et une politique de retention.
- N4: ajouter des en-tetes securite pertinents au serveur web.
- N5: rediger un plan de durcissement pre-production.

### FOAD

Construire une checklist de secrets: generation, stockage, rotation, diffusion, revocation, detection accidentelle.

## Session 08 - Supply chain et CVE

### Objectifs

Auditer les dependances sans installer volontairement une dependance dangereuse. Comprendre la difference entre alerte, exploitabilite et priorite de mise a jour.

### Exercices

- N1: expliquer `composer.lock` et `symfony.lock`.
- N2: lancer ou decrire `composer audit`.
- N3: analyser une advisory fictive et qualifier l'impact local.
- N4: proposer une politique de mise a jour.
- N5: rediger une decision de risque pour une dependance indirecte.

### FOAD

Comparer Dependabot, Renovate et `composer audit` sur les criteres: automatisation, bruit, controle, tracabilite.

## Session 09 - Concepts memoire

### Objectifs

Sortir du web pur pour comprendre pourquoi PHP/Symfony ne couvre pas les erreurs memoire natives, puis observer un crash C controle.

### Parcours

Le dossier `native-lab` contient deux programmes C: demonstration de debordement et parseur volontairement fragile.

### Exercices

- N1: compiler le programme.
- N2: observer un comportement normal puis un crash local.
- N3: activer AddressSanitizer.
- N4: corriger la copie ou la verification de taille.
- N5: relier l'exemple aux CWE memoire.

### FOAD

Rediger une note expliquant pourquoi un developpeur web doit comprendre ces concepts malgre l'usage d'un langage haut niveau.

## Session 10 - Introduction fuzzing

### Objectifs

Comprendre la logique du fuzzing: corpus, oracle, crash, minimisation, correction, regression.

### Exercices

- N1: lancer le smoke fuzzing fourni.
- N2: enrichir le corpus avec cas limites.
- N3: identifier le type d'entree qui provoque un crash.
- N4: corriger le parseur et relancer.
- N5: proposer un mini plan de fuzzing pour un endpoint web.

### FOAD

Presenter en dix lignes la difference entre fuzzing aleatoire, fuzzing guide par couverture et property-based testing.

## Session 11 - Methodologie audit

### Objectifs

Transformer une collection d'observations en audit structure: perimetre, preuves, impact, vraisemblance, priorisation et remediation.

### Exercices

- N1: relire toutes les fiches produites.
- N2: fusionner les doublons et separer les constats distincts.
- N3: classer par criticite.
- N4: rediger un resume executif court.
- N5: preparer le plan de test de l'audit final.

### FOAD

Choisir trois constats et ameliorer leur redaction pour qu'un responsable applicatif puisse agir sans cours supplementaire.

## Session 12 - Audit final

### Objectifs

Realiser un audit applicatif court, borne et defendable. Produire un rapport et soutenir les choix de priorisation.

### Deroule

- 30 min: cadrage et repartition.
- 90 min: tests et collecte de preuves.
- 45 min: verification et tri.
- 45 min: redaction.
- 30 min: restitution courte.

### Livrable

Le rapport final doit contenir 5 a 8 constats priorises, dont au moins un sur API/access control, un sur injection ou XSS, un sur configuration/logs et un sur cycle de developpement.

### FOAD

Finaliser le rapport, ajouter les annexes techniques et preparer une soutenance de 5 minutes.
