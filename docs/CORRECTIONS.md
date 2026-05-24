# Corrections detaillees

## Session 01

Correction attendue: surface d'attaque comprenant web, API, fichiers, auth, admin, logs, Docker et native-lab. Les risques majeurs sont access control, injection, XSS/CSRF, SSRF et upload.

## Session 02 - SQLi

Cause: `EventRepository::vulnerableSearch()` concatene `q` dans SQL.

Remediation possible:

```php
$sql = 'SELECT * FROM event WHERE published = true AND (LOWER(title) LIKE LOWER(:term) OR LOWER(location) LIKE LOWER(:term)) ORDER BY starts_at ASC';
return $this->connection->fetchAllAssociative($sql, ['term' => '%'.$term.'%']);
```

Ajouter une limite de longueur, journaliser les erreurs sans payload complet et tester un terme contenant une apostrophe.

## Session 03 - XSS/CSRF

Cause XSS: `comment.content|raw`.

Correction: afficher `{{ comment.content }}` ou purifier explicitement si HTML autorise. Preferer texte brut.

Cause CSRF: suppression commentaire sans token ni verification auteur.

Correction: champ `_token`, `isCsrfTokenValid('delete-comment'.$comment->getId(), $token)` et controle proprietaire/admin.

## Session 05 - Access Control

Cause IDOR: `InvoiceController::download()` accepte toute facture resolue par ParamConverter.

Correction: refuser si `invoice.user !== app.user` et absence `ROLE_ADMIN`. Variante: voter `INVOICE_VIEW`.

Cause BOLA: `/api/users/{id}` expose les donnees sans verifier l'appelant.

Correction: endpoint `me` ou controle admin/proprietaire, DTO public reduit.

## Session 06 - SSRF

Cause: `HttpClient` recupere une URL fournie par l'utilisateur.

Correction: exiger scheme `https`, allowlist host, resolution DNS, blocage `127.0.0.0/8`, `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`, link-local et metadata cloud. Garder timeout court et taille limitee.

## Session 07 - Secrets/logs

Cause: faux secrets documentes, `APP_SECRET` faible, log de carte fictive.

Correction: `.env.local`, vault/variables d'environnement, rotation, redaction des logs, niveaux minimaux et retention.

## Session 08 - Supply Chain

Correction attendue: `composer audit`, qualification des advisories, mise a jour par lot maitrise, tests de non regression.

## Sessions 09/10 - Memoire/fuzzing

Correction: remplacer `strcpy` par copie bornee, verifier tailles et retours. Avec ASan, le crash doit devenir absent.

## Session 12

Rapport attendu: priorisation claire. Les constats critiques typiques sont IDOR/BOLA, SQLi, SSRF, XSS stockee et upload.
