<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Registration;
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
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', ['event' => $event]);
    }

    #[Route('/events/{id}/register', name: 'event_register', methods: ['POST'])]
    public function register(Event $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $registration = (new Registration())->setUser($this->getUser())->setEvent($event);
        $em->persist($registration);
        $em->flush();
        $this->addFlash('success', 'Inscription enregistree.');
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
