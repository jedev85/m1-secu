# Notes formateur par session

Ces notes completent le workbook etudiant. Elles ne doivent pas etre publiees sur la branche `student`.

## Principes d'animation

- Toujours partir du comportement metier avant la faille.
- Demander aux etudiants d'ecrire l'autorisation attendue avant de tester l'autorisation reelle.
- Exiger une preuve locale courte et reversible.
- Interdire les payloads destructeurs et les tests hors instance locale.
- Faire produire une verification apres chaque correction.

## Session 01 - OWASP

### Objectif formateur

Installer le vocabulaire commun et eviter que les etudiants cherchent directement des payloads. La bonne sortie de seance est une carte d'attaque propre.

### Deroule conseille

1. Faire naviguer avec un compte anonyme, puis `ROLE_USER`, puis admin.
2. Construire au tableau les actifs: comptes, inscriptions, factures, commentaires, fichiers, API, logs.
3. Associer chaque actif a au moins une menace.
4. Faire choisir trois zones prioritaires: factures, API utilisateur, commentaires.

### Questions utiles

- Quelle donnee a le plus de valeur dans ce metier?
- Quelle route change un etat?
- Quelle route expose un objet par identifiant?
- Qu'est-ce qu'un admin devrait pouvoir faire qu'un utilisateur ne devrait pas pouvoir faire?

### Variante rapide

Demander une mini threat model STRIDE du flux inscription -> facture.

## Session 02 - SQL Injection

### Points a faire emerger

La faille n'est pas "SQL" en general: elle vient du passage direct du parametre `q` dans une chaine SQL. Les etudiants doivent localiser `EventRepository::vulnerableSearch()`.

### Correction attendue

Utiliser un parametre lie:

```php
$sql = 'SELECT * FROM event WHERE published = true AND (LOWER(title) LIKE LOWER(:term) OR LOWER(location) LIKE LOWER(:term)) ORDER BY starts_at ASC';
return $this->connection->fetchAllAssociative($sql, ['term' => '%'.$term.'%']);
```

Ajouter ensuite une limite de longueur et un test avec un terme contenant une apostrophe.

### Pieges frequents

- Echappement manuel avec `addslashes`.
- Suppression de la recherche au lieu de la corriger.
- Correction dans le controleur alors que la cause est dans le repository.

## Session 03 - XSS et CSRF

### Points a faire emerger

Le commentaire est persistant. Le risque touche les autres utilisateurs qui consultent l'evenement. La suppression de commentaire est une action sensible POST sans token ni controle proprietaire.

### Correction attendue

- Remplacer `{{ comment.content|raw }}` par `{{ comment.content }}`.
- Ajouter un token CSRF dans le formulaire de suppression.
- Verifier auteur ou admin dans le controleur.

### Variante avancee

Autoriser un sous-ensemble HTML via un purificateur serait possible, mais ce n'est pas necessaire ici. Le choix pedagogique recommande est texte brut.

## Session 04 - SDLC DevSecOps

### Attendu

Les etudiants doivent produire une definition of done securite realiste:

- test fonctionnel pour la route modifiee;
- pas de secret commite;
- `composer validate`;
- `composer audit` quand dependances modifiees;
- preuve avant/apres dans MR;
- revue d'autorisation si objet par ID.

### Variante avancee

Faire ecrire un workflow CI GitHub Actions minimal sans forcer son execution.

## Session 05 - Auth et access control

### Points a faire emerger

Le flux inscription rend les factures visibles et comprehensibles. L'erreur est que l'application protege la page par `ROLE_USER`, mais ne verifie pas que la facture appartient a l'utilisateur courant.

### Correction attendue

Controle direct:

```php
if ($invoice->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException();
}
```

Variante preferee pour groupe avance: voter `INVOICE_VIEW`.

### Tests attendus

- user1 accede a sa facture;
- user1 ne peut pas telecharger une facture user2;
- admin peut telecharger les factures.

## Session 06 - API et SSRF

### Points a faire emerger

`/api/users/{id}` est une BOLA/exposition excessive. `/api/invoices/{id}` repete le probleme facture. `/api/preview-url` fait sortir le serveur vers une URL fournie par le client.

### Correction attendue API

- Endpoint `me` pour l'utilisateur courant ou verification proprietaire/admin.
- DTO de sortie sans `internalNote`, sans roles si non necessaires.
- CORS restreint.

### Correction attendue SSRF

- Accepter uniquement `https`.
- Allowlist de domaines pedagogiques ou liste locale controlee.
- Resolution DNS puis blocage IP privees/locales/link-local.
- Timeout court, taille limitee, pas de follow redirect aveugle.

## Session 07 - Secrets logs hardening

### Points a faire emerger

Les faux secrets ne sont pas des incidents, mais ils representent de mauvaises habitudes. Le log de carte fictive illustre le risque de fuite dans les journaux.

### Correction attendue

- Ne jamais logger de donnees sensibles.
- Remplacer par identifiants techniques et correlation ID.
- Documenter `.env.local` et variables d'environnement.
- Ajouter des headers de base cote Nginx.

## Session 08 - Supply chain

### Attendu

Les etudiants doivent montrer qu'ils savent qualifier une alerte. Une CVE critique non exploitable localement ne se traite pas comme une CVE exploitee en production, mais elle doit etre tracee.

### Questions utiles

- La dependance est-elle directe?
- Le code vulnerable est-il appele?
- Existe-t-il un patch compatible?
- Quel test protege la mise a jour?

## Session 09 - Memoire

### Attendu

Ne pas chercher l'exploit. L'objectif est de comprendre crash, depassement de buffer, ASan et correction bornee.

### Correction attendue

Remplacer les copies non bornees, verifier les longueurs et conserver un test de regression.

## Session 10 - Fuzzing

### Attendu

Les etudiants doivent distinguer "trouver un crash" et "corriger durablement". Demander une entree minimale, une hypothese cause racine, puis une regression.

### Variante avancee

Faire proposer un fuzzing d'API: generation de JSON invalides, tailles extremes, champs inconnus, types inattendus.

## Session 11 - Methodologie audit

### Attendu

Le groupe doit consolider. Un bon rapport ne liste pas seulement des bugs: il explique impact, preuve, vraisemblance et remediation.

### Grille de revue rapide

- Le titre du constat est-il actionnable?
- La preuve est-elle locale et suffisante?
- La cause racine est-elle distincte du symptome?
- La correction est-elle testable?
- La priorite est-elle justifiee?

## Session 12 - Audit final

### Organisation conseillee

Former des groupes de 2 a 3. Leur donner 90 minutes de tests, puis imposer la redaction. Un rapport court mais priorise vaut mieux qu'une liste exhaustive non qualifiee.

### Attendus minimaux

- 5 constats solides.
- Au moins 2 preuves reproductibles.
- Au moins 3 remediations concretes.
- Une synthese executive de 10 lignes.
- Une restitution orale de 5 minutes.
