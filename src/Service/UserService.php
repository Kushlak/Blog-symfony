<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(string $username, string $password, array $roles): User
    {
        $user = new User();
        $user->setUsername($username)->setPassword($password)->setRoles($roles);

        $this->userRepository->save($user);

        return $user;
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function updateUser(User $user): void
    {
        $this->userRepository->save($user);
    }

    public function getAllUsers()
    {
        $users = $this->userRepository->findAll();
    }
}
