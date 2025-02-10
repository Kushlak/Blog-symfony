<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationTemplateController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            if (!isset($data['email']) || !isset($data['password']) || !isset($data['username']) || !isset($data['firstName']) || !isset($data['lastName'])) {
                return new Response('Invalid data', Response::HTTP_BAD_REQUEST);
            }

            $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            $existingUserByUsername = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);

            if ($existingUserByEmail || $existingUserByUsername) {
                return new Response('Email or Username already in use', Response::HTTP_BAD_REQUEST);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home'); // Redirect to home or login page after registration
        }

        return $this->render('registration/register.html.twig');
    }
}
