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
}
