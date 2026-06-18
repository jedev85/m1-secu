<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }

    #[Route('/account/profile', name: 'account_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($request->isMethod('POST')) {
            foreach ($request->request->all() as $field => $value) {
                if (property_exists($user, $field)) {
                    $user->$field = $value;
                }
            }
            $em->flush();
            $this->addFlash('success', 'Profil mis a jour.');
        }

        return $this->render('account/profile.html.twig');
    }

    #[Route('/account/email', name: 'account_email', methods: ['POST'])]
    public function email(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->email = (string) $request->request->get('email');
        $em->flush();

        return $this->redirectToRoute('account');
    }

    #[Route('/account/password', name: 'account_password', methods: ['POST'])]
    public function password(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->password = $hasher->hashPassword($user, (string) $request->request->get('password', 'password'));
        $em->flush();

        return $this->redirectToRoute('account');
    }
}
