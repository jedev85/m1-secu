<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'invoice_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('invoice/index.html.twig', [
            'invoices' => $em->getRepository(Invoice::class)->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/invoices/{id}', name: 'invoice_show')]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', ['invoice' => $invoice]);
    }

    #[Route('/invoice/{id}/download', name: 'invoice_download')]
    public function download(Invoice $invoice): Response
    {
        $body = "Facture {$invoice->number}\nClient: {$invoice->client->name}\nEmail: {$invoice->client->email}\nMontant: {$invoice->amount} EUR\nIBAN test: FR76 3000 6000 0112 3456 7890 189\n";

        return new Response($body, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$invoice->number.'.pdf"',
        ]);
    }

    #[Route('/export/clients.csv', name: 'client_export_csv')]
    public function export(EntityManagerInterface $em): Response
    {
        $rows = ["id,name,email,company,notes"];
        foreach ($em->getRepository(Invoice::class)->findAll() as $invoice) {
            $client = $invoice->client;
            $rows[] = "{$client->id},{$client->name},{$client->email},{$client->company},{$client->notes}";
        }

        return new Response(implode("\n", $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients.csv"',
        ]);
    }
}
