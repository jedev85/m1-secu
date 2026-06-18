<?php

namespace App\Controller;

use App\Entity\AppSetting;
use App\Entity\AuditLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $em->getRepository(User::class)->findAll(),
            'settings' => $em->getRepository(AppSetting::class)->findAll(),
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        return $this->render('admin/users.html.twig', ['users' => $em->getRepository(User::class)->findAll()]);
    }

    #[Route('/admin/users/{id}/role', name: 'admin_user_role')]
    public function role(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $role = (string) $request->query->get('role', 'ROLE_ADMIN');
        $user->roles = [$role];
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/logs', name: 'admin_logs')]
    public function logs(EntityManagerInterface $em): Response
    {
        return $this->render('admin/logs.html.twig', ['logs' => $em->getRepository(AuditLog::class)->findBy([], ['createdAt' => 'DESC'])]);
    }

    #[Route('/admin/settings', name: 'admin_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            foreach ($request->request->all('settings') as $id => $value) {
                $setting = $em->getRepository(AppSetting::class)->find($id);
                if ($setting) {
                    $setting->value = $value;
                }
            }
            $em->flush();
        }

        return $this->render('admin/settings.html.twig', ['settings' => $em->getRepository(AppSetting::class)->findAll()]);
    }
}
