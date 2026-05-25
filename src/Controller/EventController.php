<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Invoice;
use App\Entity\Registration;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/events', name: 'event_index')]
    public function index(Request $request, EventRepository $events): Response
    {
        $q = (string) $request->query->get('q', '');
        return $this->render('event/index.html.twig', [
            'q' => $q,
            'events' => $q === '' ? $events->findPublished() : $events->vulnerableSearch($q),
            'raw_results' => $q !== '',
        ]);
    }

    #[Route('/events/{id}', name: 'event_show', requirements: ['id' => '\d+'])]
    public function show(Event $event, EntityManagerInterface $em): Response
    {
        $registration = null;
        if ($this->getUser() instanceof User) {
            $registration = $em->getRepository(Registration::class)->findOneBy([
                'event' => $event,
                'user' => $this->getUser(),
            ]);
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'lab' => $this->labForEvent($event),
            'registration' => $registration,
            'registrationCount' => $event->getRegistrations()->count(),
            'remainingSeats' => max(0, $event->getCapacity() - $event->getRegistrations()->count()),
        ]);
    }

    #[Route('/events/{id}/register', name: 'event_register', methods: ['POST'])]
    public function register(Event $event, EntityManagerInterface $em, string $invoicesDir): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var User $user */
        $user = $this->getUser();

        $existing = $em->getRepository(Registration::class)->findOneBy([
            'event' => $event,
            'user' => $user,
        ]);
        if ($existing instanceof Registration) {
            $this->addFlash('info', 'Vous êtes déjà inscrit à cet événement.');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        $registration = (new Registration())->setUser($user)->setEvent($event);
        $em->persist($registration);

        if (!is_dir($invoicesDir)) {
            mkdir($invoicesDir, 0775, true);
        }
        $invoiceNumber = sprintf('INV-%s-E%03d-U%03d', date('Ymd'), $event->getId(), $user->getId());
        $invoiceFile = $invoiceNumber.'.txt';
        file_put_contents($invoicesDir.'/'.$invoiceFile, "Facture fictive {$invoiceNumber}\nClient: {$user->getEmail()}\nÉvénement: {$event->getTitle()}\nMontant cents: {$event->getPriceCents()}\n");
        $em->persist((new Invoice())
            ->setUser($user)
            ->setNumber($invoiceNumber)
            ->setAmountCents($event->getPriceCents())
            ->setFilePath($invoiceFile));

        $em->flush();
        $this->addFlash('success', 'Inscription confirmée. Une facture fictive a été ajoutée à votre espace.');
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }

    #[Route('/events/{id}/comments', name: 'comment_create', methods: ['POST'])]
    public function comment(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $comment = (new Comment())
            ->setEvent($event)
            ->setAuthor($this->getUser())
            ->setContent((string) $request->request->get('content'));
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }

    #[Route('/comments/{id}/delete', name: 'comment_delete', methods: ['POST'])]
    public function deleteComment(Comment $comment, EntityManagerInterface $em): Response
    {
        $eventId = $comment->getEvent()->getId();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('event_show', ['id' => $eventId]);
    }

    private function labForEvent(Event $event): array
    {
        $title = strtolower($event->getTitle());

        if (str_contains($title, 'sql injection')) {
            return $this->enrichLab([
                'theme' => 'SQL Injection',
                'goal' => 'Tester la recherche événement et relier le comportement observé au repository.',
                'entrypoints' => [
                    ['label' => 'Recherche événement', 'href' => $this->generateUrl('event_index').'?q=Lyon'],
                    ['label' => 'Fiche session SQLi', 'href' => '/docs/SESSION_02_SQLI.md'],
                ],
                'tasks' => [
                    'Observer le paramètre q sur la liste des événements.',
                    'Comparer recherche normale et entrée atypique.',
                    'Localiser la construction SQL côté repository.',
                ],
            ], 'sqli');
        }

        if (str_contains($title, 'xss') || str_contains($title, 'csrf')) {
            return $this->enrichLab([
                'theme' => 'XSS stockée et CSRF',
                'goal' => 'Utiliser les commentaires de cet événement comme zone de test navigateur.',
                'entrypoints' => [
                    ['label' => 'Commentaires de cette page', 'href' => '#comments'],
                    ['label' => 'Fiche session XSS/CSRF', 'href' => '/docs/SESSION_03_XSS_CSRF.md'],
                ],
                'tasks' => [
                    'Publier un commentaire avec un contenu HTML contrôlé.',
                    'Vérifier le rendu avec un autre compte.',
                    'Examiner la suppression de commentaire.',
                ],
            ], 'xss_csrf');
        }

        if (str_contains($title, 'api') || str_contains($title, 'ssrf')) {
            return $this->enrichLab([
                'theme' => 'API, BOLA et SSRF',
                'goal' => 'Relier les données événement, utilisateur et facture aux endpoints JSON.',
                'entrypoints' => [
                    ['label' => 'API événements', 'href' => '/api/events'],
                    ['label' => 'API utilisateur de démo', 'href' => '/api/users/1'],
                    ['label' => 'Preview URL', 'href' => '/api/preview-url'],
                    ['label' => 'Fiche session API/SSRF', 'href' => '/docs/SESSION_06_API_SSRF.md'],
                ],
                'tasks' => [
                    'Comparer les données web et JSON.',
                    'Tester les identifiants directs sur utilisateurs et factures.',
                    'Analyser la fonctionnalité de preview URL.',
                ],
            ], 'api_ssrf');
        }

        if (str_contains($title, 'devsecops')) {
            return $this->enrichLab([
                'theme' => 'SDLC et DevSecOps',
                'goal' => 'Transformer une faille observée en changement livré proprement.',
                'entrypoints' => [
                    ['label' => 'Makefile', 'href' => '/docs/INSTALLATION.md'],
                    ['label' => 'Fiche session SDLC', 'href' => '/docs/SESSION_04_SDLC_DEVSECOPS.md'],
                ],
                'tasks' => [
                    'Identifier les commandes de vérification disponibles.',
                    'Proposer un contrôle CI pour une correction sécurité.',
                    'Rédiger une définition of done sécurité.',
                ],
            ], 'sdlc');
        }

        if (str_contains($title, 'hardening')) {
            return $this->enrichLab([
                'theme' => 'Security misconfiguration',
                'goal' => 'Auditer les choix de configuration Symfony, Nginx, cookies et erreurs.',
                'entrypoints' => [
                    ['label' => 'Architecture', 'href' => '/docs/ARCHITECTURE.md'],
                    ['label' => 'Fiche secrets/logs/hardening', 'href' => '/docs/SESSION_07_SECRETS_LOGS_HARDENING.md'],
                ],
                'tasks' => [
                    'Observer les headers HTTP.',
                    'Lire la configuration security.yaml.',
                    'Identifier les informations trop visibles en environnement local.',
                ],
            ], 'hardening');
        }

        if (str_contains($title, 'secrets')) {
            return $this->enrichLab([
                'theme' => 'Secrets et logs',
                'goal' => 'Identifier les mauvaises pratiques documentées et les logs trop bavards.',
                'entrypoints' => [
                    ['label' => 'Bad practices', 'href' => '/docs/bad-practices.md'],
                    ['label' => 'Guide FOAD', 'href' => '/docs/FOAD_GUIDE.md'],
                ],
                'tasks' => [
                    'Distinguer faux secret pédagogique et vrai secret.',
                    'Localiser un log contenant une donnée sensible fictive.',
                    'Proposer une politique de rédaction.',
                ],
            ], 'secrets');
        }

        if (str_contains($title, 'upload')) {
            return $this->enrichLab([
                'theme' => 'Upload vulnerable',
                'goal' => 'Tester le flux avatar dans le profil et le stockage public des fichiers.',
                'entrypoints' => [
                    ['label' => 'Édition profil', 'href' => $this->generateUrl('profile_edit')],
                    ['label' => 'Banque exercices upload', 'href' => '/docs/EXERCISE_BANK.md'],
                ],
                'tasks' => [
                    'Observer le nom de fichier conservé.',
                    'Vérifier le chemin public de l avatar.',
                    'Lister les validations attendues.',
                ],
            ], 'upload');
        }

        if (str_contains($title, 'fuzzing')) {
            return $this->enrichLab([
                'theme' => 'Mémoire et fuzzing',
                'goal' => 'Utiliser native-lab pour observer crash, correction et régression.',
                'entrypoints' => [
                    ['label' => 'Session mémoire', 'href' => '/docs/SESSION_09_MEMORY.md'],
                    ['label' => 'Session fuzzing', 'href' => '/docs/SESSION_10_FUZZING.md'],
                ],
                'tasks' => [
                    'Compiler le mini programme C.',
                    'Observer un crash local contrôlé.',
                    'Relancer après correction.',
                ],
            ], 'fuzzing');
        }

        if (str_contains($title, 'audit final')) {
            return $this->enrichLab([
                'theme' => 'Audit final',
                'goal' => 'Assembler les constats web, API, configuration et cycle de développement.',
                'entrypoints' => [
                    ['label' => 'Sujet audit final', 'href' => '/docs/evaluations/AUDIT_FINAL.md'],
                    ['label' => 'Workbook', 'href' => '/docs/STUDENT_WORKBOOK.md'],
                ],
                'tasks' => [
                    'Prioriser 5 à 8 constats.',
                    'Associer preuve, impact et remédiation.',
                    'Préparer la restitution courte.',
                ],
            ], 'audit');
        }

        return $this->enrichLab([
            'theme' => 'Exploration métier',
            'goal' => 'Utiliser cet événement pour comprendre le flux inscription, commentaires et factures.',
            'entrypoints' => [
                ['label' => 'Profil', 'href' => $this->generateUrl('profile_show')],
                ['label' => 'Factures', 'href' => $this->generateUrl('invoice_index')],
                ['label' => 'Workbook', 'href' => '/docs/STUDENT_WORKBOOK.md'],
            ],
            'tasks' => [
                "S’inscrire à l’événement.",
                'Vérifier le profil et la facture générée.',
                "Relier le flux aux contrôles d'accès attendus.",
            ],
        ], 'default');
    }

    private function enrichLab(array $lab, string $key): array
    {
        $details = [
            'sqli' => [
                'estimatedTime' => '3h30 présentiel + 1h30 FOAD',
                'scenario' => 'Le service marketing veut rechercher rapidement les événements par ville ou thème. La recherche semble anodine, mais elle traverse directement HTTP, contrôleur, repository et base de données. Les étudiants doivent montrer comment une entrée utilisateur peut modifier le comportement attendu, puis corriger sans supprimer la fonctionnalité.',
                'concepts' => ['Flux entrée utilisateur', 'SQL concaténé', 'Doctrine DBAL', 'Requête paramétrée', 'Test de non-régression', 'CWE-89'],
                'levels' => [
                    ['N1', 'Tracer le paramètre q depuis l’URL jusqu au repository et noter chaque transformation.'],
                    ['N2', 'Provoquer un comportement anormal local, non destructeur, puis expliquer ce que cela prouve et ce que cela ne prouve pas.'],
                    ['N3', 'Proposer une correction par paramètre lié ou QueryBuilder en conservant la recherche par ville et titre.'],
                    ['N4', 'Ajouter garde-fous: longueur maximale, cas vide, test avec apostrophe, message utilisateur propre.'],
                    ['N5', 'Rédiger une fiche d’audit avec cause racine, impact métier, preuve, remédiation et vérification.'],
                ],
                'deliverables' => ['Trace du flux q', 'Preuve locale courte', 'Patch proposé', 'Test ou procédure de vérification', 'Fiche SQLi'],
                'checks' => ['Une recherche normale fonctionne encore.', 'Une apostrophe ne casse plus la requête.', 'Le code ne concatene plus q dans SQL.', 'La correction’est placée près de la cause racine.'],
                'vigilance' => ['Pas de payload destructeur.', 'Pas d’exfiltration massive.', 'Ne pas remplacer la faille par une validation fragile uniquement côté HTML.'],
            ],
            'xss_csrf' => [
                'estimatedTime' => '3h30 présentiel + 2h FOAD',
                'scenario' => 'Les participants échangent sous les événements. Un commentaire publié par un utilisateur est relu par d’autres comptes, ce qui transforme un simple champ texte en surface navigateur persistante. La suppression de commentaire sert ensuite à discuter les actions sensibles et les tokens CSRF.',
                'concepts' => ['XSS stockée', 'Contexte HTML Twig', 'Échappement automatique', 'CSRF', 'Action POST sensible', 'Controle auteur/admin'],
                'levels' => [
                    ['N1', 'Identifier tous les champs utilisateur affichés sur la page événement.'],
                    ['N2', 'Démontrer une altération visuelle contrôlée dans le navigateur local et verifier la persistance avec un second compte.'],
                    ['N3', 'Corriger le rendu du commentaire et ajouter un token CSRF sur la suppression.'],
                    ['N4', 'Ajouter la vérification auteur ou admin et tester les cas user1, user2 et admin.'],
                    ['N5', 'Séparer deux constats: XSS stockée et action sensible insuffisamment protégée.'],
                ],
                'deliverables' => ['Capture locale avant correction', 'Analyse du contexte Twig', 'Patch Twig et contrôleur', 'Vérification multi-comptes', 'Deux fiches d’audit séparées'],
                'checks' => ['Le commentaire reste lisible comme texte.', 'Le navigateur n’interprète plus le contenu utilisateur comme HTML.', 'La suppression sans token’est refusée.', 'Un utilisateur ne supprime pas les commentaires des autres sauf rôle admin.'],
                'vigilance' => ['Ne pas publier de payload offensif avancé.', 'Ne pas confondre filtrage en entrée et échappement en sortie.', 'Penser au comportement pour les commentaires déjà stockés.'],
            ],
            'api_ssrf' => [
                'estimatedTime' => '3h30 présentiel + 2h FOAD',
                'scenario' => 'EventSecure expose une API pour partenaires et une fonction de preview URL pour enrichir les événements. Les étudiants doivent comparer les données visibles dans le web et dans l API, puis analyser le risque d’un serveur qui contacte une URL choisie par le client.',
                'concepts' => ['BOLA', 'Exposition excessive', 'DTO de sortie', 'CORS', 'SSRF', 'Filtrage IP privées/locales', 'Timeout'],
                'levels' => [
                    ['N1', 'Inventorier endpoints, méthodes, schémas JSON et données exposées.'],
                    ['N2', 'Comparer les réponses pour plusieurs IDs utilisateur et facture avec deux comptes.'],
                    ['N3', 'Proposer des contrôles propriétaire/admin et des DTO réduits.'],
                    ['N4', 'Définir une politique SSRF: scheme, allowlist, DNS, IP privées, redirections, taille et timeout.'],
                    ['N5', 'Produire un rapport API priorisé avec tests automatisables.'],
                ],
                'deliverables' => ['Table des endpoints', 'Schéma JSON observé', 'Constats BOLA/API data exposure', 'Plan de correction SSRF', 'Tests proposés'],
                'checks' => ['Un utilisateur ne lit pas les objets d un autre.', 'Les champs internes disparaissent des réponses publiques.', 'CORS est justifié.', 'Les URL locales et privées sont refusées après résolution.'],
                'vigilance' => ['Tester uniquement des cibles locales contrôlées.', 'Ne pas appeler de services externes non nécessaires.', 'Revalider les redirections.'],
            ],
            'sdlc' => [
                'estimatedTime' => '3h30 présentiel + 1h30 FOAD',
                'scenario' => 'Une faille corrigée sans test ni méthode revient souvent plus tard. Cette séance transforme les observations précédentes en exigences de livraison: preuve avant/après, test, revue, audit de dépendances et définition of done sécurité.',
                'concepts' => ['Définition of done', 'CI minimale', 'Composer validate/audit', 'Tests fonctionnels', 'Revue de code sécurité', 'Régression'],
                'levels' => [
                    ['N1', 'Lister les commandes Makefile et expliquer ce qu’elles vérifient.'],
                    ['N2', 'Identifier les contrôles absents dans un pipeline minimal.'],
                    ['N3', 'Écrire un test de non-régression pour une faille déjà étudiée.'],
                    ['N4', 'Proposer un workflow CI et une checklist merge request.'],
                    ['N5', 'Défendre une politique de merge pour corrections sécurité urgentes.'],
                ],
                'deliverables' => ['Checklist MR', 'Définition of done', 'Test de régression proposé', 'Plan CI minimal'],
                'checks' => ['La checklist mentionne preuve, impact et vérification.', 'La CI reste exécutable localement.', 'Les contrôles sont proportionnés au projet.'],
                'vigilance' => ['Ne pas confondre outil et processus.', 'Éviter une CI théorique impossible à maintenir.', 'Ne pas bloquer les corrections urgentes sans voie contrôlée.'],
            ],
            'hardening' => [
                'estimatedTime' => '3h30 présentiel + 1h30 FOAD',
                'scenario' => 'L’application fonctionne en local, mais plusieurs choix de configuration seraient inacceptables en production. Les étudiants doivent distinguer ce qui est volontaire pour le lab, ce qui est acceptable en dev, et ce qui doit être durci avant un déploiement réel.',
                'concepts' => ['Security headers', 'Cookies', 'Routes admin', 'Pages d’erreur', 'Nginx', 'Symfony Security', 'A05 Security Misconfiguration'],
                'levels' => [
                    ['N1', 'Observer headers, cookies et configuration security.yaml.'],
                    ['N2', 'Identifier les routes ou réglages trop permissifs.'],
                    ['N3', 'Proposer des changements de configuration minimaux.'],
                    ['N4', 'Définir des contrôles différents pour dev, test et prod.'],
                    ['N5', 'Rédiger un plan de hardening pre-production priorisé.'],
                ],
                'deliverables' => ['Inventaire configuration', 'Plan hardening', 'Justification dev vs prod', 'Vérification headers/cookies'],
                'checks' => ['Les headers ajoutés ne cassent pas les pages.', 'Les routes admin gardent le comportement attendu.', 'Les choix spécifiques lab sont documentés.'],
                'vigilance' => ['Ne pas appliquer une CSP complexe sans test.', 'Ne pas masquer une faille pédagogique sans la documenter.', 'Séparer environnement local et production.'],
            ],
            'secrets' => [
                'estimatedTime' => '3h30 présentiel + 1h30 FOAD',
                'scenario' => 'Les secrets du lab sont fictifs, mais les mauvaises habitudes qu’ils illustrent sont réelles: valeurs dans fichiers, logs trop bavards, absence de rotation et confusion entre configuration et secret.',
                'concepts' => ['Secret fictif', 'Variable environnement', 'Rotation', 'Rédaction logs', 'Rétention', 'Incident secret commis'],
                'levels' => [
                    ['N1', 'Repérer les faux secrets et les logs sensibles fictifs.'],
                    ['N2', 'Classer les informations selon sensibilité et besoin de journalisation.'],
                    ['N3', 'Proposer une rédaction de logs et une gestion via environnement.'],
                    ['N4', 'Écrire une procédure courte de rotation et révocation.'],
                    ['N5', 'Rédiger une note incident: secret poussé dans Git, impact, actions et prévention.'],
                ],
                'deliverables' => ['Inventaire secrets/logs', 'Politique de rédaction', 'Procédure de rotation', 'Fiche incident'],
                'checks' => ['Aucun vrai secret n’est introduit.', 'Les logs gardent une valeur d’investigation.', 'La procédure indique qui fait quoi et quand.'],
                'vigilance' => ['Ne jamais remplacer les faux secrets par de vrais.', 'Ne pas logger les données de paiement même fictives dans un exemple de production.', 'Penser aux sauvegardes et historiques Git.'],
            ],
            'upload' => [
                'estimatedTime' => '3h30 présentiel + 1h30 FOAD',
                'scenario' => 'Le profil accepte un avatar. Ce flux paraît secondaire, mais il combine entrée fichier, nom fourni par le client, stockage public et absence de validation robuste. Les étudiants doivent raisonner sur la chaîne complète, pas seulement sur l’extension.',
                'concepts' => ['Upload public', 'Nom de fichier client', 'MIME', 'Extension allowlist', 'Stockage hors webroot', 'CWE-434'],
                'levels' => [
                    ['N1', 'Localiser le formulaire, le contrôleur et le répertoire de stockage.'],
                    ['N2', 'Observer comment le nom et le chemin public sont construits.'],
                    ['N3', 'Proposer validation taille, extension, MIME et nom aléatoire.'],
                    ['N4', 'Déplacer le stockage hors public et servir via contrôleur autorisé.'],
                    ['N5', 'Rédiger une fiche upload avec risques, contrôles et limites.'],
                ],
                'deliverables' => ['Schéma du flux upload', 'Liste des validations manquantes', 'Patch proposé', 'Vérification accès fichier'],
                'checks' => ['Le nom original n’est plus utilisé tel quel.', 'Les fichiers non attendus sont refusés.', 'Le fichier ne devient pas public sans contrôle.'],
                'vigilance' => ['Ne pas exécuter de fichier upload.', 'Ne pas supposer que l’extension suffit.', 'Vérifier collisions et traversal.'],
            ],
            'fuzzing' => [
                'estimatedTime' => '7h présentiel réparties sessions 09/10 + 3h FOAD',
                'scenario' => 'Le sous-dossier native-lab sort volontairement du cadre Symfony. Il sert à montrer les limites des langages haut niveau face aux composants natifs, aux parseurs et aux erreurs de taille.',
                'concepts' => ['Buffer overflow', 'Crash', 'AddressSanitizer', 'Corpus', 'Oracle', 'Régression', 'CWE mémoire'],
                'levels' => [
                    ['N1', 'Compiler les programmes et lancer les cas normaux.'],
                    ['N2', 'Observer un crash local et lire le rapport ASan.'],
                    ['N3', 'Corriger la gestion de taille ou de copie.'],
                    ['N4', 'Ajouter un corpus de cas limites et relancer.'],
                    ['N5', 'Comparer fuzzing natif et tests robustesse sur API web.'],
                ],
                'deliverables' => ['Commandes exécutées', 'Trace crash ou ASan', 'Hypothèse cause racine', 'Correction et régression'],
                'checks' => ['Le programme compile.', 'Le crash est compris sans exploit avancé.', 'La correction ne casse pas les cas valides.'],
                'vigilance' => ['Objectif crash et compréhension, pas exploitation.', 'Rester dans native-lab.', 'Documenter les limites de la démonstration.'],
            ],
            'audit' => [
                'estimatedTime' => '3h30 présentiel + 2h FOAD',
                'scenario' => 'La dernière séance simule une mission courte: peu de temps, beaucoup de signaux, obligation de prioriser. Les étudiants doivent produire un rapport défendable plutôt qu une liste brute.',
                'concepts' => ['Périmètre', 'Preuve', 'Impact', 'Vraisemblance', 'Priorisation', 'Remédiation', 'Restitution'],
                'levels' => [
                    ['N1', 'Lister les surfaces et choisir le périmètre.'],
                    ['N2', 'Collecter des preuves locales courtes.'],
                    ['N3', 'Écrire 5 à 8 constats avec remédiation.'],
                    ['N4', 'Ajouter vérification ou test pour les corrections majeures.'],
                    ['N5', 'Présenter une synthèse exécutive et défendre la priorisation.'],
                ],
                'deliverables' => ['Rapport final', 'Tableau priorisé', 'Annexes techniques', 'Restitution 5 minutes'],
                'checks' => ['Chaque constat a preuve, impact et correction.', 'Les risques critiques sont en premier.', 'Les limites de l audit sont explicites.'],
                'vigilance' => ['Éviter l inventaire non qualifié.', 'Ne pas gonfler artificiellement la criticité.', 'Respecter le périmètre local.'],
            ],
            'default' => [
                'estimatedTime' => '1h à 2h selon profondeur',
                'scenario' => 'Cet événement sert à comprendre le métier avant les failles: consultation, inscription, commentaire, facture et profil. Sans ce parcours, les vulnérabilités restent abstraites.',
                'concepts' => ['Workflow métier', 'État applicatif', 'Relations Doctrine', "Contrôle d'accès attendu", 'Données fictives'],
                'levels' => [
                    ['N1', 'S inscrire et observer les changements visibles.'],
                    ['N2', 'Relier chaque changement a une entité Doctrine.'],
                    ['N3', 'Identifier les contrôles attendus sur chaque objet créé.'],
                    ['N4', 'Proposer un test fonctionnel du parcours.'],
                    ['N5', 'Transformer le parcours en scénario d’audit.'],
                ],
                'deliverables' => ['Carte du flux inscription', 'Liste des objets créés', 'Contrôles attendus', 'Questions d’audit'],
                'checks' => ['L’inscription apparaît sur l’événement et le profil.', 'Une facture fictive est créée.', 'Le flux reste cohérent avec plusieurs comptes.'],
                'vigilance' => ['Ne pas traiter le métier comme décoratif.', 'Vérifier les effets de bord.', 'Distinguer UX et sécurité.'],
            ],
        ];

        return array_merge($lab, $details[$key] ?? $details['default']);
    }
}
