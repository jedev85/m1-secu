<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Document;
use App\Entity\Invoice;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('dashboard.html.twig', [
            'clientCount' => $em->getRepository(Client::class)->count([]),
            'ticketCount' => $em->getRepository(Ticket::class)->count([]),
            'invoiceCount' => $em->getRepository(Invoice::class)->count([]),
            'documentCount' => $em->getRepository(Document::class)->count([]),
        ]);
    }
}
