<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;

class RegistrationTemplateController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            $existingUserByUsername = $entityManager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

            if ($existingUserByEmail || $existingUserByUsername) {
                return new Response('Email or Username already in use', Response::HTTP_BAD_REQUEST);
            }

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
