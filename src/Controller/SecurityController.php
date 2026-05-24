<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void {}

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = (new User())
                ->setEmail((string) $request->request->get('email'))
                ->setFullName((string) $request->request->get('fullName'))
                ->setCompany((string) $request->request->get('company'))
                ->setPassword($hasher->hashPassword(new User(), (string) $request->request->get('password')));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Compte cree. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig');
    }
}
