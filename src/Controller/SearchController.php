<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, EntityManagerInterface $em): Response
    {
        $q = (string) $request->query->get('q', '');
        $sort = (string) $request->query->get('sort', 'name');
        $dir = (string) $request->query->get('direction', 'ASC');

        // Recherche historique: conserve les filtres libres utilises par les equipes metier.
        $dql = "SELECT c FROM App\\Entity\\Client c WHERE c.name LIKE '%".$q."%' OR c.company LIKE '%".$q."%' ORDER BY c.".$sort.' '.$dir;
        $clients = $em->createQuery($dql)->getResult();

        $tickets = $em->createQueryBuilder()
            ->select('t')
            ->from('App\\Entity\\Ticket', 't')
            ->where('t.title LIKE :q')
            ->setParameter('q', '%'.$q.'%')
            ->getQuery()
            ->getResult();
        $invoices = $em->createQueryBuilder()
            ->select('i')
            ->from('App\\Entity\\Invoice', 'i')
            ->join('i.client', 'ic')
            ->where('i.number LIKE :q OR ic.email LIKE :q')
            ->setParameter('q', '%'.$q.'%')
            ->getQuery()
            ->getResult();

        return $this->render('search/index.html.twig', compact('q', 'clients', 'tickets', 'invoices'));
    }
}
