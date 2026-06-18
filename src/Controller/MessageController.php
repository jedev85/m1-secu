<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'message_inbox')]
    public function inbox(EntityManagerInterface $em): Response
    {
        return $this->render('message/index.html.twig', [
            'title' => 'Boite de reception',
            'messages' => $em->getRepository(Message::class)->findBy(['recipient' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/messages/sent', name: 'message_sent')]
    public function sent(EntityManagerInterface $em): Response
    {
        return $this->render('message/index.html.twig', [
            'title' => 'Messages envoyes',
            'messages' => $em->getRepository(Message::class)->findBy(['sender' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/messages/new', name: 'message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $message = new Message();
        if ($request->isMethod('POST')) {
            $message->sender = $this->getUser();
            $message->recipient = $em->getRepository(User::class)->find($request->request->get('recipient_id'));
            $message->subject = (string) $request->request->get('subject');
            $message->body = (string) $request->request->get('body');
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('message_sent');
        }

        return $this->render('message/form.html.twig', ['message' => $message, 'users' => $em->getRepository(User::class)->findAll()]);
    }

    #[Route('/messages/{id}', name: 'message_show')]
    public function show(Message $message, EntityManagerInterface $em): Response
    {
        $message->readAt ??= new \DateTimeImmutable();
        $em->flush();

        return $this->render('message/show.html.twig', ['message' => $message]);
    }

    #[Route('/messages/{id}/delete', name: 'message_delete')]
    public function delete(Message $message, EntityManagerInterface $em): Response
    {
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute('message_inbox');
    }
}
