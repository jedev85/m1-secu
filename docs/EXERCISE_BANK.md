# Banque d'exercices

Cette banque permet au formateur de choisir selon le niveau du groupe. Les exercices sont redondants volontairement: il n'est pas necessaire de tout faire.

## Echelle de difficulte

- N1: observation et comprehension.
- N2: preuve locale controlee.
- N3: correction.
- N4: durcissement et tests.
- N5: formalisation audit.

## Recherche evenement et SQLi

1. N1: tracer le chemin complet `q` depuis la requete HTTP jusqu'a SQL.
2. N1: comparer le retour de `findPublished()` et de la recherche.
3. N2: provoquer une erreur locale non destructive avec une entree inattendue.
4. N2: expliquer ce que l'erreur revele sur la construction SQL.
5. N3: parametrer la requete DBAL.
6. N3: reecrire avec QueryBuilder Doctrine.
7. N4: ajouter une limite de longueur et un test avec apostrophe.
8. N5: rediger une fiche CWE-89.

## Commentaires, XSS et CSRF

1. N1: identifier les champs affiches dans `event/show.html.twig`.
2. N1: expliquer le role de l'echappement Twig.
3. N2: demontrer une alteration visuelle locale via commentaire.
4. N2: observer la persistance pour un second utilisateur.
5. N3: supprimer l'affichage non echappe.
6. N3: ajouter token CSRF sur suppression.
7. N4: verifier que seul l'auteur ou un admin peut supprimer.
8. N5: comparer XSS stockee, reflechie et DOM XSS.

## Inscriptions et factures

1. N1: s'inscrire a un evenement et verifier profil + facture.
2. N1: expliquer les entites `Event`, `Registration`, `Invoice`.
3. N2: tester l'acces a une facture par identifiant.
4. N3: ajouter un controle proprietaire.
5. N3: appliquer le meme controle a l'API facture.
6. N4: concevoir un voter Symfony.
7. N4: ajouter un test fonctionnel avec deux utilisateurs.
8. N5: rediger une matrice d'autorisation.

## API publique/interne

1. N1: inventorier les endpoints et methodes.
2. N1: ecrire le schema JSON observe.
3. N2: comparer `/api/users/1` et `/api/users/2`.
4. N2: identifier les donnees exposees sans besoin metier.
5. N3: creer une reponse reduite.
6. N3: ajouter controle utilisateur courant/admin.
7. N4: restreindre CORS.
8. N5: produire un mini rapport OWASP API Top 10.

## Profile et mass assignment

1. N1: lire les champs modifiables via formulaire.
2. N2: tester un payload JSON avec un champ non expose par l'UI.
3. N3: remplacer la boucle de setters par une allowlist.
4. N3: separer formulaire web et API.
5. N4: introduire un DTO valide.
6. N5: expliquer pourquoi `method_exists` n'est pas une politique d'autorisation.

## Upload

1. N1: localiser le repertoire d'upload.
2. N2: tester extension, nom de fichier et acces public.
3. N3: generer un nom aleatoire.
4. N3: limiter les extensions et verifier MIME.
5. N4: stocker hors webroot et servir via controleur autorise.
6. N5: rediger une fiche CWE-434.

## SSRF

1. N1: documenter l'appel `preview-url`.
2. N2: tester uniquement des cibles locales controlees.
3. N3: limiter les schemes.
4. N3: refuser IP privees/locales apres resolution DNS.
5. N4: ajouter timeout, taille max et journalisation sobre.
6. N5: expliquer le risque cloud metadata sans le reproduire.

## Logs et secrets

1. N1: identifier faux secrets et logs sensibles fictifs.
2. N2: expliquer pourquoi une donnee fictive reste utile pedagogiquement.
3. N3: redacter les logs.
4. N4: proposer variables d'environnement et rotation.
5. N5: rediger une procedure d'incident en cas de secret commite.

## Native lab

1. N1: compiler et lancer les programmes.
2. N2: observer un crash avec ASan.
3. N3: corriger la taille de buffer.
4. N4: ajouter cas de regression.
5. N5: relier au secure coding C.

## Audit final

1. N1: lister 10 observations.
2. N2: produire 5 preuves locales.
3. N3: proposer 5 corrections.
4. N4: ajouter tests ou verifications.
5. N5: livrer un rapport priorise et defendable.
