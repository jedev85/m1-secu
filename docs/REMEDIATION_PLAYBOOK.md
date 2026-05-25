# Playbook de remediation

Ce document fournit des pistes de correction. Il ne remplace pas la discussion en cours: plusieurs corrections sont possibles si elles sont coherentes, testees et justifiees.

## SQLi

Correction minimale:

```php
public function safeSearch(string $term): array
{
    $sql = 'SELECT * FROM event WHERE published = true AND (LOWER(title) LIKE LOWER(:term) OR LOWER(location) LIKE LOWER(:term)) ORDER BY starts_at ASC';
    return $this->connection->fetchAllAssociative($sql, ['term' => '%'.$term.'%']);
}
```

Verification:

- recherche normale retourne des evenements;
- apostrophe ne provoque pas d'erreur SQL;
- chaine tres longue est refusee ou tronquee.

## XSS

Correction minimale:

```twig
<div>{{ comment.content }}</div>
```

Verification:

- le texte du commentaire reste visible;
- les caracteres HTML sont affiches comme texte;
- aucun code HTML fourni par l'utilisateur n'est interprete.

## CSRF et suppression commentaire

Correction minimale:

```twig
<input type="hidden" name="_token" value="{{ csrf_token('delete-comment' ~ comment.id) }}">
```

```php
if (!$this->isCsrfTokenValid('delete-comment'.$comment->getId(), (string) $request->request->get('_token'))) {
    throw $this->createAccessDeniedException();
}
if ($comment->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException();
}
```

## Factures IDOR

Correction minimale:

```php
if ($invoice->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException();
}
```

Correction avancee:

- creer `InvoiceVoter`;
- utiliser `denyAccessUnlessGranted('INVOICE_VIEW', $invoice)`;
- tester proprietaire, autre utilisateur et admin.

## API BOLA

Correction minimale:

```php
if ($user !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException();
}
return $this->json([
    'id' => $user->getId(),
    'email' => $user->getEmail(),
    'fullName' => $user->getFullName(),
    'company' => $user->getCompany(),
]);
```

Ne pas exposer `internalNote`, roles ou donnees support sans besoin metier.

## Mass assignment

Correction minimale:

```php
$allowed = ['fullName', 'company', 'phone'];
foreach ($allowed as $field) {
    if (array_key_exists($field, $payload)) {
        $setter = 'set'.ucfirst($field);
        $user->{$setter}((string) $payload[$field]);
    }
}
```

Correction avancee:

- DTO separe;
- validation Symfony;
- mapping explicite;
- tests de rejet de `roles`.

## Upload

Correction minimale:

- allowlist extensions;
- verification MIME;
- nom aleatoire;
- taille maximale;
- refus des noms fournis par le client.

Correction avancee:

- stockage hors `public`;
- controleur de telechargement avec autorisation;
- antivirus ou analyse asynchrone selon contexte.

## SSRF

Etapes de remediation:

1. Parser l'URL.
2. Refuser scheme absent ou non autorise.
3. Resoudre le host.
4. Refuser loopback, private, link-local, multicast.
5. Garder timeout court.
6. Limiter taille lue.
7. Desactiver redirections ou revalider chaque redirection.

Verification:

- URL publique de test autorisee;
- `localhost`, `127.0.0.1`, `10.0.0.0/8`, `192.168.0.0/16` refuses;
- erreur utilisateur claire sans fuite technique.

## Logs

Remplacer:

```php
$logger->info('Profile updated', ['email' => $user->getEmail(), 'demo_card' => '4111-1111-1111-1111']);
```

Par:

```php
$logger->info('Profile updated', ['user_id' => $user->getId()]);
```

## Nginx headers

Exemples a discuter:

```nginx
add_header X-Content-Type-Options nosniff always;
add_header Referrer-Policy strict-origin-when-cross-origin always;
add_header X-Frame-Options DENY always;
```

Ne pas ajouter une CSP complexe sans verifier les assets utilises.
