<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InvoiceController extends AbstractController
{
    #[Route('/invoices', name: 'invoice_index')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('invoice/index.html.twig', [
            'invoices' => $em->getRepository(Invoice::class)->findBy(['user' => $user], ['issuedAt' => 'DESC']),
        ]);
    }

    #[Route('/invoices/{id}/download', name: 'invoice_download')]
    public function download(Invoice $invoice, string $invoicesDir): BinaryFileResponse
    {
        return $this->file($invoicesDir.'/'.$invoice->getFilePath(), $invoice->getNumber().'.txt');
    }
}
