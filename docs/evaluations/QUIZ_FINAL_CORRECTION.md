# Correction quiz final

Ce fichier est reserve a la branche `teacher`.

Les reponses doivent etre evaluees sur trois axes: exactitude technique, contextualisation EventSecure, remediation verifiable.

Points cles:

- IDOR/BOLA: controle proprietaire ou role cote serveur, pas seulement UI.
- SSRF: allowlist, blocage reseaux internes, timeout, taille limitee.
- Mass assignment: DTO/allowlist, jamais appel dynamique de setters sur payload brut.
- Upload: nom aleatoire, stockage maitrise, validation MIME/extension, taille limitee.
- Supply chain: `composer audit`, qualification, mise a jour, exception documentee.
- Memoire/fuzzing: crash controle, ASan, pas d'exploit avance.
- Logs/secrets: redaction, pas de secrets Git, rotation.
- Hardening: CSP, HSTS en prod, frame-ancestors, CORS restreint, cookies `Secure`, `HttpOnly`, `SameSite`.

Attribuer credit partiel si le raisonnement est correct meme si la syntaxe Symfony exacte manque.
