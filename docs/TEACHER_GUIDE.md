# Guide formateur

## Positionnement

EventSecure Lab sert de fil rouge sur 42h presentiel et 21h FOAD. La branche `teacher` contient les corrections et pistes d'animation. La branche `student` doit rester la seule distribuee aux etudiants.

Pour tenir un volume long, utiliser conjointement:

- `PROGRAMME_62H.md` pour le rythme horaire;
- `STUDENT_WORKBOOK.md` pour les consignes distribuees;
- `EXERCISE_BANK.md` pour adapter la difficulte;
- `TEACHER_SESSION_NOTES.md` pour l'animation;
- `REMEDIATION_PLAYBOOK.md` pour les corrections techniques;
- `GRADING_RUBRIC.md` pour l'evaluation.

## Deroule conseille

- Sessions 01 a 03: prise en main, OWASP, SQLi, XSS/CSRF.
- Sessions 04 a 06: SDLC, access control, API/SSRF.
- Sessions 07 a 08: durcissement, secrets, logs, chaine d'approvisionnement logicielle.
- Sessions 09 a 10: memoire et fuzzing dans `native-lab`.
- Sessions 11 a 12: methodologie puis audit final.

## Animation

Commencer chaque faille par observation puis laisser les etudiants formuler l'impact. Limiter les demonstrations a des preuves locales non destructrices. Exiger une verification apres chaque correction.

## Corrections attendues par theme

- SQLi: remplacer concatenation par parametres DBAL ou QueryBuilder.
- XSS: supprimer `raw`, valider le contexte, tester un commentaire persistant.
- CSRF: ajouter token, verifier cote controleur.
- IDOR/BOLA: verifier proprietaire ou role, reduire les donnees API.
- Mass assignment: DTO ou allowlist stricte.
- Upload: extension/MIME allowlist, nom aleatoire, stockage non public si possible.
- SSRF: allowlist, resolution DNS, blocage IP privees/locales, timeouts.
- Logs: redaction, niveau adapte, correlation sans secrets.
- Misconfiguration: headers, CORS restreint, cookies durcis.

## Variantes rapides

Pour un groupe avance, demander un voter Symfony pour factures et un test fonctionnel par correction. Pour un groupe debutant, se limiter a l'identification et a la fiche d'audit.

## Flux metier a expliquer

Le flux evenement sert de colonne vertebrale:

1. un utilisateur consulte un evenement;
2. il s'inscrit;
3. une inscription devient visible dans son profil;
4. une facture fictive est generee;
5. les routes facture/API deviennent auditables;
6. les commentaires alimentent XSS/CSRF.

Ce point est important: sans ce flux, les etudiants ont l'impression que les evenements sont decoratifs. Il faut le demontrer des la session 01.
