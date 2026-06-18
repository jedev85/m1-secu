<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/clients', name: 'client_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $clients = $em->getRepository(Client::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('client/index.html.twig', ['clients' => $clients]);
    }

    #[Route('/clients/new', name: 'client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Client();
        if ($request->isMethod('POST')) {
            $this->fill($client, $request);
            $client->owner = $this->getUser();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_show', ['id' => $client->id]);
        }

        return $this->render('client/form.html.twig', ['client' => $client]);
    }

    #[Route('/clients/{id}', name: 'client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', ['client' => $client]);
    }

    #[Route('/clients/{id}/edit', name: 'client_edit', methods: ['GET', 'POST'])]
    public function edit(Client $client, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $this->fill($client, $request);
            $ownerId = $request->request->get('owner_id');
            if ($ownerId) {
                $client->owner = $em->getRepository(User::class)->find($ownerId);
            }
            $em->flush();

            return $this->redirectToRoute('client_show', ['id' => $client->id]);
        }

        return $this->render('client/form.html.twig', ['client' => $client]);
    }

    #[Route('/clients/{id}/delete', name: 'client_delete')]
    public function delete(Client $client, EntityManagerInterface $em): Response
    {
        $em->remove($client);
        $em->flush();

        return $this->redirectToRoute('client_index');
    }

    private function fill(Client $client, Request $request): void
    {
        $client->name = (string) $request->request->get('name');
        $client->email = (string) $request->request->get('email');
        $client->phone = (string) $request->request->get('phone');
        $client->company = (string) $request->request->get('company');
        $client->notes = (string) $request->request->get('notes');
    }
}
