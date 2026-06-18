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
        $type = (string) $request->query->get('type', 'all');
        $sort = (string) $request->query->get('sort', 'name');
        $dir = (string) $request->query->get('direction', 'ASC');
        $status = (string) $request->query->get('status', '');

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
        $projects = $type === 'clients' ? [] : $em->createQuery("SELECT p FROM App\\Entity\\Project p WHERE p.name LIKE '%".$q."%'".($status ? " AND p.status = '".$status."'" : ''))->getResult();
        $notes = $type === 'clients' ? [] : $em->createQueryBuilder()->select('n')->from('App\\Entity\\InternalNote', 'n')->where('n.title LIKE :q OR n.content LIKE :q')->setParameter('q', '%'.$q.'%')->getQuery()->getResult();
        $messages = $type === 'clients' ? [] : $em->createQueryBuilder()->select('m')->from('App\\Entity\\Message', 'm')->where('m.subject LIKE :q OR m.body LIKE :q')->setParameter('q', '%'.$q.'%')->getQuery()->getResult();
        $users = $type === 'clients' ? [] : $em->createQueryBuilder()->select('u')->from('App\\Entity\\User', 'u')->where('u.email LIKE :q OR u.fullName LIKE :q')->setParameter('q', '%'.$q.'%')->getQuery()->getResult();

        return $this->render('search/index.html.twig', compact('q', 'clients', 'tickets', 'invoices', 'projects', 'notes', 'messages', 'users'));
    }
}
