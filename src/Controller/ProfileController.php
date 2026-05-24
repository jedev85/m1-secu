<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_show')]
    public function show(): Response
    {
        return $this->render('profile/show.html.twig');
    }

    #[Route('/profile/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, LoggerInterface $logger, string $uploadsDir): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            $user->setEmail((string) $request->request->get('email', $user->getEmail()));
            $user->setFullName((string) $request->request->get('fullName', $user->getFullName()));
            $user->setCompany((string) $request->request->get('company', $user->getCompany()));
            $user->setPhone((string) $request->request->get('phone', $user->getPhone()));

            $roles = $request->request->all('roles');
            if ($roles !== []) {
                $user->setRoles($roles);
            }

            $avatar = $request->files->get('avatar');
            if ($avatar instanceof UploadedFile) {
                $name = $avatar->getClientOriginalName();
                $avatar->move($uploadsDir.'/avatars', $name);
                $user->setAvatarPath('/uploads/avatars/'.$name);
            }

            $logger->info('Profile updated', ['email' => $user->getEmail(), 'demo_card' => '4111-1111-1111-1111']);
            $em->flush();
            return $this->redirectToRoute('profile_show');
        }

        return $this->render('profile/edit.html.twig');
    }
}
