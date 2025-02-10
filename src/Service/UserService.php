<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function createUser(string $username, string $password, array $roles): User
    {
        $user = new User();
        $user->setUsername($username)->setPassword($password)->setRoles($roles);
// Persist user to the database $this->entityManager->persist($user); $this->entityManager->flush();
        return $user;
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function updateUser(User $user): void
    {
        $this->entityManager->flush();
    }
}
