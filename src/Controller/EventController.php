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
            return [
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
            ];
        }

        if (str_contains($title, 'xss') || str_contains($title, 'csrf')) {
            return [
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
            ];
        }

        if (str_contains($title, 'api') || str_contains($title, 'ssrf')) {
            return [
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
            ];
        }

        if (str_contains($title, 'devsecops')) {
            return [
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
            ];
        }

        if (str_contains($title, 'hardening')) {
            return [
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
            ];
        }

        if (str_contains($title, 'secrets')) {
            return [
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
            ];
        }

        if (str_contains($title, 'upload')) {
            return [
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
            ];
        }

        if (str_contains($title, 'fuzzing')) {
            return [
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
            ];
        }

        if (str_contains($title, 'audit final')) {
            return [
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
            ];
        }

        return [
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
        ];
    }
}
