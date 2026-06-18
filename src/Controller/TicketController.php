<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'ticket_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $em->getRepository(Ticket::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/tickets/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();
        if ($request->isMethod('POST')) {
            $ticket->title = (string) $request->request->get('title');
            $ticket->description = (string) $request->request->get('description');
            $ticket->priority = (string) $request->request->get('priority', 'normal');
            $ticket->customer = $em->getRepository(Client::class)->find($request->request->get('client_id'));
            $ticket->createdBy = $this->getUser();
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('ticket_show', ['id' => $ticket->id]);
        }

        return $this->render('ticket/form.html.twig', [
            'ticket' => $ticket,
            'clients' => $em->getRepository(Client::class)->findAll(),
        ]);
    }

    #[Route('/tickets/{id}', name: 'ticket_show')]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', ['ticket' => $ticket]);
    }

    #[Route('/tickets/{id}/comment', name: 'ticket_comment', methods: ['POST'])]
    public function comment(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $comment->ticket = $ticket;
        $comment->author = $this->getUser();
        $comment->content = (string) $request->request->get('content');
        $em->persist($comment);
        $em->flush();

        return $this->redirectToRoute('ticket_show', ['id' => $ticket->id]);
    }

    #[Route('/tickets/{id}/status', name: 'ticket_status')]
    public function status(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $ticket->status = (string) $request->query->get('status', 'closed');
        $em->flush();

        return $this->redirectToRoute('ticket_show', ['id' => $ticket->id]);
    }

    #[Route('/tickets/{id}/assign', name: 'ticket_assign')]
    public function assign(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $ticket->assignedTo = $em->getRepository(User::class)->find($request->query->get('user'));
        $em->flush();

        return $this->redirectToRoute('ticket_show', ['id' => $ticket->id]);
    }
}
