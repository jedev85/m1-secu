<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ActivityController extends AbstractController
{
    #[Route('/admin/activity', name: 'activity_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $q = (string) $request->query->get('q', '');
        $logs = $q
            ? $em->createQuery("SELECT l FROM App\\Entity\\ActivityLog l WHERE l.action LIKE '%".$q."%' OR l.details LIKE '%".$q."%'")->getResult()
            : $em->getRepository(ActivityLog::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('activity/index.html.twig', ['logs' => $logs, 'q' => $q]);
    }

    #[Route('/admin/activity.csv', name: 'activity_export')]
    public function export(EntityManagerInterface $em): Response
    {
        $rows = ['id,actor,action,target,details'];
        foreach ($em->getRepository(ActivityLog::class)->findAll() as $log) {
            $rows[] = "{$log->id},{$log->actor?->email},{$log->action},{$log->targetType}:{$log->targetId},{$log->details}";
        }

        return new Response(implode("\n", $rows), 200, ['Content-Type' => 'text/csv']);
    }
}
