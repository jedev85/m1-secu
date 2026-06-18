<?php

namespace App\Controller;

use App\Entity\AppSetting;
use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/clients', name: 'api_clients')]
    public function clients(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Client $client) => [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'company' => $client->company,
            'notes' => $client->notes,
            'owner' => ['id' => $client->owner?->id, 'email' => $client->owner?->email, 'roles' => $client->owner?->roles],
        ], $em->getRepository(Client::class)->findAll()));
    }

    #[Route('/api/clients/{id}', name: 'api_client_show')]
    public function client(Client $client): JsonResponse
    {
        return $this->json([
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'phone' => $client->phone,
            'company' => $client->company,
            'notes' => $client->notes,
            'createdAt' => $client->createdAt->format(DATE_ATOM),
            'owner' => ['id' => $client->owner?->id, 'email' => $client->owner?->email, 'roles' => $client->owner?->roles],
        ]);
    }

    #[Route('/api/tickets', name: 'api_tickets')]
    public function tickets(EntityManagerInterface $em): JsonResponse
    {
        return $this->json(array_map(fn (Ticket $ticket) => [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'customerEmail' => $ticket->customer?->email,
            'createdBy' => $ticket->createdBy?->email,
            'comments' => array_map(fn ($comment) => $comment->content, $ticket->comments->toArray()),
        ], $em->getRepository(Ticket::class)->findAll()));
    }

    #[Route('/api/invoices/{id}', name: 'api_invoice_show')]
    public function invoice(Invoice $invoice): JsonResponse
    {
        return $this->json([
            'id' => $invoice->id,
            'number' => $invoice->number,
            'amount' => $invoice->amount,
            'status' => $invoice->status,
            'pdfPath' => $invoice->pdfPath,
            'owner' => $invoice->owner?->email,
            'client' => ['name' => $invoice->client?->name, 'email' => $invoice->client?->email, 'notes' => $invoice->client?->notes],
        ]);
    }

    #[Route('/api/debug/config', name: 'api_debug_config')]
    public function debugConfig(EntityManagerInterface $em): JsonResponse
    {
        return $this->json([
            'settings' => array_map(fn (AppSetting $setting) => [$setting->name => $setting->value], $em->getRepository(AppSetting::class)->findAll()),
            'database_url' => $_ENV['DATABASE_URL'] ?? null,
            'app_secret' => $_ENV['APP_SECRET'] ?? null,
        ]);
    }
}
