<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $em->getRepository(User::class)->count([]),
            'events' => $em->getRepository(Event::class)->count([]),
            'comments' => $em->getRepository(Comment::class)->count([]),
        ]);
    }

    #[Route('/reports', name: 'admin_reports')]
    public function reports(EntityManagerInterface $em): Response
    {
        return $this->render('admin/reports.html.twig', [
            'recentUsers' => $em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC'], 10),
        ]);
    }
}
