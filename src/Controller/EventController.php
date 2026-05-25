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
            $this->addFlash('info', 'Vous etes deja inscrit a cet evenement.');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        $registration = (new Registration())->setUser($user)->setEvent($event);
        $em->persist($registration);

        if (!is_dir($invoicesDir)) {
            mkdir($invoicesDir, 0775, true);
        }
        $invoiceNumber = sprintf('INV-%s-E%03d-U%03d', date('Ymd'), $event->getId(), $user->getId());
        $invoiceFile = $invoiceNumber.'.txt';
        file_put_contents($invoicesDir.'/'.$invoiceFile, "Facture fictive {$invoiceNumber}\nClient: {$user->getEmail()}\nEvenement: {$event->getTitle()}\nMontant cents: {$event->getPriceCents()}\n");
        $em->persist((new Invoice())
            ->setUser($user)
            ->setNumber($invoiceNumber)
            ->setAmountCents($event->getPriceCents())
            ->setFilePath($invoiceFile));

        $em->flush();
        $this->addFlash('success', 'Inscription confirmee. Une facture fictive a ete ajoutee a votre espace.');
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
                'goal' => 'Tester la recherche evenement et relier le comportement observe au repository.',
                'entrypoints' => [
                    ['label' => 'Recherche evenement', 'href' => $this->generateUrl('event_index').'?q=Lyon'],
                    ['label' => 'Fiche session SQLi', 'href' => '/docs/SESSION_02_SQLI.md'],
                ],
                'tasks' => [
                    'Observer le parametre q sur la liste des evenements.',
                    'Comparer recherche normale et entree atypique.',
                    'Localiser la construction SQL cote repository.',
                ],
            ], 'sqli');
        }

        if (str_contains($title, 'xss') || str_contains($title, 'csrf')) {
            return $this->enrichLab([
                'theme' => 'XSS stockee et CSRF',
                'goal' => 'Utiliser les commentaires de cet evenement comme zone de test navigateur.',
                'entrypoints' => [
                    ['label' => 'Commentaires de cette page', 'href' => '#comments'],
                    ['label' => 'Fiche session XSS/CSRF', 'href' => '/docs/SESSION_03_XSS_CSRF.md'],
                ],
                'tasks' => [
                    'Publier un commentaire avec un contenu HTML controle.',
                    'Verifier le rendu avec un autre compte.',
                    'Examiner la suppression de commentaire.',
                ],
            ], 'xss_csrf');
        }

        if (str_contains($title, 'api') || str_contains($title, 'ssrf')) {
            return $this->enrichLab([
                'theme' => 'API, BOLA et SSRF',
                'goal' => 'Relier les donnees evenement, utilisateur et facture aux endpoints JSON.',
                'entrypoints' => [
                    ['label' => 'API evenements', 'href' => '/api/events'],
                    ['label' => 'API utilisateur de demo', 'href' => '/api/users/1'],
                    ['label' => 'Preview URL', 'href' => '/api/preview-url'],
                    ['label' => 'Fiche session API/SSRF', 'href' => '/docs/SESSION_06_API_SSRF.md'],
                ],
                'tasks' => [
                    'Comparer les donnees web et JSON.',
                    'Tester les identifiants directs sur utilisateurs et factures.',
                    'Analyser la fonctionnalite de preview URL.',
                ],
            ], 'api_ssrf');
        }

        if (str_contains($title, 'devsecops')) {
            return $this->enrichLab([
                'theme' => 'SDLC et DevSecOps',
                'goal' => 'Transformer une faille observee en changement livre proprement.',
                'entrypoints' => [
                    ['label' => 'Makefile', 'href' => '/docs/INSTALLATION.md'],
                    ['label' => 'Fiche session SDLC', 'href' => '/docs/SESSION_04_SDLC_DEVSECOPS.md'],
                ],
                'tasks' => [
                    'Identifier les commandes de verification disponibles.',
                    'Proposer un controle CI pour une correction securite.',
                    'Rediger une definition of done securite.',
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
                'goal' => 'Identifier les mauvaises pratiques documentees et les logs trop bavards.',
                'entrypoints' => [
                    ['label' => 'Bad practices', 'href' => '/docs/bad-practices.md'],
                    ['label' => 'Guide FOAD', 'href' => '/docs/FOAD_GUIDE.md'],
                ],
                'tasks' => [
                    'Distinguer faux secret pedagogique et vrai secret.',
                    'Localiser un log contenant une donnee sensible fictive.',
                    'Proposer une politique de redaction.',
                ],
            ], 'secrets');
        }

        if (str_contains($title, 'upload')) {
            return $this->enrichLab([
                'theme' => 'Upload vulnerable',
                'goal' => 'Tester le flux avatar dans le profil et le stockage public des fichiers.',
                'entrypoints' => [
                    ['label' => 'Edition profil', 'href' => $this->generateUrl('profile_edit')],
                    ['label' => 'Banque exercices upload', 'href' => '/docs/EXERCISE_BANK.md'],
                ],
                'tasks' => [
                    'Observer le nom de fichier conserve.',
                    'Verifier le chemin public de l avatar.',
                    'Lister les validations attendues.',
                ],
            ], 'upload');
        }

        if (str_contains($title, 'fuzzing')) {
            return $this->enrichLab([
                'theme' => 'Memoire et fuzzing',
                'goal' => 'Utiliser native-lab pour observer crash, correction et regression.',
                'entrypoints' => [
                    ['label' => 'Session memoire', 'href' => '/docs/SESSION_09_MEMORY.md'],
                    ['label' => 'Session fuzzing', 'href' => '/docs/SESSION_10_FUZZING.md'],
                ],
                'tasks' => [
                    'Compiler le mini programme C.',
                    'Observer un crash local controle.',
                    'Relancer apres correction.',
                ],
            ], 'fuzzing');
        }

        if (str_contains($title, 'audit final')) {
            return $this->enrichLab([
                'theme' => 'Audit final',
                'goal' => 'Assembler les constats web, API, configuration et cycle de developpement.',
                'entrypoints' => [
                    ['label' => 'Sujet audit final', 'href' => '/docs/evaluations/AUDIT_FINAL.md'],
                    ['label' => 'Workbook', 'href' => '/docs/STUDENT_WORKBOOK.md'],
                ],
                'tasks' => [
                    'Prioriser 5 a 8 constats.',
                    'Associer preuve, impact et remediation.',
                    'Preparer la restitution courte.',
                ],
            ], 'audit');
        }

        return $this->enrichLab([
            'theme' => 'Exploration metier',
            'goal' => 'Utiliser cet evenement pour comprendre le flux inscription, commentaires et factures.',
            'entrypoints' => [
                ['label' => 'Profil', 'href' => $this->generateUrl('profile_show')],
                ['label' => 'Factures', 'href' => $this->generateUrl('invoice_index')],
                ['label' => 'Workbook', 'href' => '/docs/STUDENT_WORKBOOK.md'],
            ],
            'tasks' => [
                'S inscrire a l evenement.',
                'Verifier le profil et la facture generee.',
                'Relier le flux aux controles d acces attendus.',
            ],
        ], 'default');
    }

    private function enrichLab(array $lab, string $key): array
    {
        $details = [
            'sqli' => [
                'estimatedTime' => '3h30 presentiel + 1h30 FOAD',
                'scenario' => 'Le service marketing veut rechercher rapidement les evenements par ville ou theme. La recherche semble anodine, mais elle traverse directement HTTP, controleur, repository et base de donnees. Les etudiants doivent montrer comment une entree utilisateur peut modifier le comportement attendu, puis corriger sans supprimer la fonctionnalite.',
                'concepts' => ['Flux entree utilisateur', 'SQL concatene', 'Doctrine DBAL', 'Requete parametree', 'Test de non-regression', 'CWE-89'],
                'levels' => [
                    ['N1', 'Tracer le parametre q depuis l URL jusqu au repository et noter chaque transformation.'],
                    ['N2', 'Provoquer un comportement anormal local, non destructeur, puis expliquer ce que cela prouve et ce que cela ne prouve pas.'],
                    ['N3', 'Proposer une correction par parametre lie ou QueryBuilder en conservant la recherche par ville et titre.'],
                    ['N4', 'Ajouter garde-fous: longueur maximale, cas vide, test avec apostrophe, message utilisateur propre.'],
                    ['N5', 'Rediger une fiche d audit avec cause racine, impact metier, preuve, remediation et verification.'],
                ],
                'deliverables' => ['Trace du flux q', 'Preuve locale courte', 'Patch propose', 'Test ou procedure de verification', 'Fiche SQLi'],
                'checks' => ['Une recherche normale fonctionne encore.', 'Une apostrophe ne casse plus la requete.', 'Le code ne concatene plus q dans SQL.', 'La correction est placee pres de la cause racine.'],
                'vigilance' => ['Pas de payload destructeur.', 'Pas d exfiltration massive.', 'Ne pas remplacer la faille par une validation fragile uniquement cote HTML.'],
            ],
            'xss_csrf' => [
                'estimatedTime' => '3h30 presentiel + 2h FOAD',
                'scenario' => 'Les participants echangent sous les evenements. Un commentaire publie par un utilisateur est relu par d autres comptes, ce qui transforme un simple champ texte en surface navigateur persistante. La suppression de commentaire sert ensuite a discuter les actions sensibles et les tokens CSRF.',
                'concepts' => ['XSS stockee', 'Contexte HTML Twig', 'Echappement automatique', 'CSRF', 'Action POST sensible', 'Controle auteur/admin'],
                'levels' => [
                    ['N1', 'Identifier tous les champs utilisateur affiches sur la page evenement.'],
                    ['N2', 'Demontrer une alteration visuelle controlee dans le navigateur local et verifier la persistance avec un second compte.'],
                    ['N3', 'Corriger le rendu du commentaire et ajouter un token CSRF sur la suppression.'],
                    ['N4', 'Ajouter la verification auteur ou admin et tester les cas user1, user2 et admin.'],
                    ['N5', 'Separer deux constats: XSS stockee et action sensible insuffisamment protegee.'],
                ],
                'deliverables' => ['Capture locale avant correction', 'Analyse du contexte Twig', 'Patch Twig et controleur', 'Verification multi-comptes', 'Deux fiches d audit separees'],
                'checks' => ['Le commentaire reste lisible comme texte.', 'Le navigateur n interprete plus le contenu utilisateur comme HTML.', 'La suppression sans token est refusee.', 'Un utilisateur ne supprime pas les commentaires des autres sauf role admin.'],
                'vigilance' => ['Ne pas publier de payload offensif avance.', 'Ne pas confondre filtrage en entree et echappement en sortie.', 'Penser au comportement pour les commentaires deja stockes.'],
            ],
            'api_ssrf' => [
                'estimatedTime' => '3h30 presentiel + 2h FOAD',
                'scenario' => 'EventSecure expose une API pour partenaires et une fonction de preview URL pour enrichir les evenements. Les etudiants doivent comparer les donnees visibles dans le web et dans l API, puis analyser le risque d un serveur qui contacte une URL choisie par le client.',
                'concepts' => ['BOLA', 'Exposition excessive', 'DTO de sortie', 'CORS', 'SSRF', 'Filtrage IP privees/locales', 'Timeout'],
                'levels' => [
                    ['N1', 'Inventorier endpoints, methodes, schemas JSON et donnees exposees.'],
                    ['N2', 'Comparer les reponses pour plusieurs IDs utilisateur et facture avec deux comptes.'],
                    ['N3', 'Proposer des controles proprietaire/admin et des DTO reduits.'],
                    ['N4', 'Definir une politique SSRF: scheme, allowlist, DNS, IP privees, redirections, taille et timeout.'],
                    ['N5', 'Produire un rapport API priorise avec tests automatisables.'],
                ],
                'deliverables' => ['Table des endpoints', 'Schema JSON observe', 'Constats BOLA/API data exposure', 'Plan de correction SSRF', 'Tests proposes'],
                'checks' => ['Un utilisateur ne lit pas les objets d un autre.', 'Les champs internes disparaissent des reponses publiques.', 'CORS est justifie.', 'Les URL locales et privees sont refusees apres resolution.'],
                'vigilance' => ['Tester uniquement des cibles locales controlees.', 'Ne pas appeler de services externes non necessaires.', 'Revalider les redirections.'],
            ],
            'sdlc' => [
                'estimatedTime' => '3h30 presentiel + 1h30 FOAD',
                'scenario' => 'Une faille corrigee sans test ni methode revient souvent plus tard. Cette seance transforme les observations precedentes en exigences de livraison: preuve avant/apres, test, revue, audit de dependances et definition of done securite.',
                'concepts' => ['Definition of done', 'CI minimale', 'Composer validate/audit', 'Tests fonctionnels', 'Revue de code securite', 'Regression'],
                'levels' => [
                    ['N1', 'Lister les commandes Makefile et expliquer ce qu elles verifient.'],
                    ['N2', 'Identifier les controles absents dans un pipeline minimal.'],
                    ['N3', 'Ecrire un test de non-regression pour une faille deja etudiee.'],
                    ['N4', 'Proposer un workflow CI et une checklist merge request.'],
                    ['N5', 'Defendre une politique de merge pour corrections securite urgentes.'],
                ],
                'deliverables' => ['Checklist MR', 'Definition of done', 'Test de regression propose', 'Plan CI minimal'],
                'checks' => ['La checklist mentionne preuve, impact et verification.', 'La CI reste executable localement.', 'Les controles sont proportionnes au projet.'],
                'vigilance' => ['Ne pas confondre outil et processus.', 'Eviter une CI theorique impossible a maintenir.', 'Ne pas bloquer les corrections urgentes sans voie controlee.'],
            ],
            'hardening' => [
                'estimatedTime' => '3h30 presentiel + 1h30 FOAD',
                'scenario' => 'L application fonctionne en local, mais plusieurs choix de configuration seraient inacceptables en production. Les etudiants doivent distinguer ce qui est volontaire pour le lab, ce qui est acceptable en dev, et ce qui doit etre durci avant un deploiement reel.',
                'concepts' => ['Security headers', 'Cookies', 'Routes admin', 'Pages d erreur', 'Nginx', 'Symfony Security', 'A05 Security Misconfiguration'],
                'levels' => [
                    ['N1', 'Observer headers, cookies et configuration security.yaml.'],
                    ['N2', 'Identifier les routes ou reglages trop permissifs.'],
                    ['N3', 'Proposer des changements de configuration minimaux.'],
                    ['N4', 'Definir des controles differents pour dev, test et prod.'],
                    ['N5', 'Rediger un plan de hardening pre-production priorise.'],
                ],
                'deliverables' => ['Inventaire configuration', 'Plan hardening', 'Justification dev vs prod', 'Verification headers/cookies'],
                'checks' => ['Les headers ajoutes ne cassent pas les pages.', 'Les routes admin gardent le comportement attendu.', 'Les choix specifiques lab sont documentes.'],
                'vigilance' => ['Ne pas appliquer une CSP complexe sans test.', 'Ne pas masquer une faille pedagogique sans la documenter.', 'Separer environnement local et production.'],
            ],
            'secrets' => [
                'estimatedTime' => '3h30 presentiel + 1h30 FOAD',
                'scenario' => 'Les secrets du lab sont fictifs, mais les mauvaises habitudes qu ils illustrent sont reelles: valeurs dans fichiers, logs trop bavards, absence de rotation et confusion entre configuration et secret.',
                'concepts' => ['Secret fictif', 'Variable environnement', 'Rotation', 'Redaction logs', 'Retention', 'Incident secret commite'],
                'levels' => [
                    ['N1', 'Reperer les faux secrets et les logs sensibles fictifs.'],
                    ['N2', 'Classer les informations selon sensibilite et besoin de journalisation.'],
                    ['N3', 'Proposer une redaction de logs et une gestion via environnement.'],
                    ['N4', 'Ecrire une procedure courte de rotation et revocation.'],
                    ['N5', 'Rediger une note incident: secret pousse dans Git, impact, actions et prevention.'],
                ],
                'deliverables' => ['Inventaire secrets/logs', 'Politique de redaction', 'Procedure de rotation', 'Fiche incident'],
                'checks' => ['Aucun vrai secret n est introduit.', 'Les logs gardent une valeur d investigation.', 'La procedure indique qui fait quoi et quand.'],
                'vigilance' => ['Ne jamais remplacer les faux secrets par de vrais.', 'Ne pas logger les donnees de paiement meme fictives dans un exemple de production.', 'Penser aux sauvegardes et historiques Git.'],
            ],
            'upload' => [
                'estimatedTime' => '3h30 presentiel + 1h30 FOAD',
                'scenario' => 'Le profil accepte un avatar. Ce flux parait secondaire, mais il combine entree fichier, nom fourni par le client, stockage public et absence de validation robuste. Les etudiants doivent raisonner sur la chaine complete, pas seulement sur l extension.',
                'concepts' => ['Upload public', 'Nom de fichier client', 'MIME', 'Extension allowlist', 'Stockage hors webroot', 'CWE-434'],
                'levels' => [
                    ['N1', 'Localiser le formulaire, le controleur et le repertoire de stockage.'],
                    ['N2', 'Observer comment le nom et le chemin public sont construits.'],
                    ['N3', 'Proposer validation taille, extension, MIME et nom aleatoire.'],
                    ['N4', 'Deplacer le stockage hors public et servir via controleur autorise.'],
                    ['N5', 'Rediger une fiche upload avec risques, controles et limites.'],
                ],
                'deliverables' => ['Schema du flux upload', 'Liste des validations manquantes', 'Patch propose', 'Verification acces fichier'],
                'checks' => ['Le nom original n est plus utilise tel quel.', 'Les fichiers non attendus sont refuses.', 'Le fichier ne devient pas public sans controle.'],
                'vigilance' => ['Ne pas executer de fichier upload.', 'Ne pas supposer que l extension suffit.', 'Verifier collisions et traversal.'],
            ],
            'fuzzing' => [
                'estimatedTime' => '7h presentiel reparties sessions 09/10 + 3h FOAD',
                'scenario' => 'Le sous-dossier native-lab sort volontairement du cadre Symfony. Il sert a montrer les limites des langages haut niveau face aux composants natifs, aux parseurs et aux erreurs de taille.',
                'concepts' => ['Buffer overflow', 'Crash', 'AddressSanitizer', 'Corpus', 'Oracle', 'Regression', 'CWE memoire'],
                'levels' => [
                    ['N1', 'Compiler les programmes et lancer les cas normaux.'],
                    ['N2', 'Observer un crash local et lire le rapport ASan.'],
                    ['N3', 'Corriger la gestion de taille ou de copie.'],
                    ['N4', 'Ajouter un corpus de cas limites et relancer.'],
                    ['N5', 'Comparer fuzzing natif et tests robustesse sur API web.'],
                ],
                'deliverables' => ['Commandes executees', 'Trace crash ou ASan', 'Hypothese cause racine', 'Correction et regression'],
                'checks' => ['Le programme compile.', 'Le crash est compris sans exploit avance.', 'La correction ne casse pas les cas valides.'],
                'vigilance' => ['Objectif crash et comprehension, pas exploitation.', 'Rester dans native-lab.', 'Documenter les limites de la demonstration.'],
            ],
            'audit' => [
                'estimatedTime' => '3h30 presentiel + 2h FOAD',
                'scenario' => 'La derniere seance simule une mission courte: peu de temps, beaucoup de signaux, obligation de prioriser. Les etudiants doivent produire un rapport defendable plutot qu une liste brute.',
                'concepts' => ['Perimetre', 'Preuve', 'Impact', 'Vraisemblance', 'Priorisation', 'Remediation', 'Restitution'],
                'levels' => [
                    ['N1', 'Lister les surfaces et choisir le perimetre.'],
                    ['N2', 'Collecter des preuves locales courtes.'],
                    ['N3', 'Ecrire 5 a 8 constats avec remediation.'],
                    ['N4', 'Ajouter verification ou test pour les corrections majeures.'],
                    ['N5', 'Presenter une synthese executive et defendre la priorisation.'],
                ],
                'deliverables' => ['Rapport final', 'Tableau priorise', 'Annexes techniques', 'Restitution 5 minutes'],
                'checks' => ['Chaque constat a preuve, impact et correction.', 'Les risques critiques sont en premier.', 'Les limites de l audit sont explicites.'],
                'vigilance' => ['Eviter l inventaire non qualifie.', 'Ne pas gonfler artificiellement la criticite.', 'Respecter le perimetre local.'],
            ],
            'default' => [
                'estimatedTime' => '1h a 2h selon profondeur',
                'scenario' => 'Cet evenement sert a comprendre le metier avant les failles: consultation, inscription, commentaire, facture et profil. Sans ce parcours, les vulnerabilites restent abstraites.',
                'concepts' => ['Workflow metier', 'Etat applicatif', 'Relations Doctrine', 'Controle d acces attendu', 'Donnees fictives'],
                'levels' => [
                    ['N1', 'S inscrire et observer les changements visibles.'],
                    ['N2', 'Relier chaque changement a une entite Doctrine.'],
                    ['N3', 'Identifier les controles attendus sur chaque objet cree.'],
                    ['N4', 'Proposer un test fonctionnel du parcours.'],
                    ['N5', 'Transformer le parcours en scenario d audit.'],
                ],
                'deliverables' => ['Carte du flux inscription', 'Liste des objets crees', 'Controles attendus', 'Questions d audit'],
                'checks' => ['L inscription apparait sur l evenement et le profil.', 'Une facture fictive est creee.', 'Le flux reste coherent avec plusieurs comptes.'],
                'vigilance' => ['Ne pas traiter le metier comme decoratif.', 'Verifier les effets de bord.', 'Distinguer UX et securite.'],
            ],
        ];

        return array_merge($lab, $details[$key] ?? $details['default']);
    }
}
