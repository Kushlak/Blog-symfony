<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin/users')]
#[IsGranted('ROLE_ADMIN')] class UserController extends AbstractController
{
    private UserService $userService;
    private SerializerInterface $serializer;

    public function __construct(UserService $userService, SerializerInterface $serializer)
    {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'api_admin_user_index', methods: ['GET'])] public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        $data = $this->serializer->serialize($users, 'json');
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/create', name: 'api_admin_user_create', methods: ['POST'])] public function create(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
// Validate data as needed
        $username = $data['username'];
        $password = $data['password'];
        $roles = $data['roles'] ?? ['ROLE_USER'];
// Password hashing $hashedPassword = $this->passwordHasher->hashPassword(new User(), $password);
        $user = $this->userService->createUser($username, $hashedPassword, $roles);
        $responseData = $this->serializer->serialize($user, 'json');
        return new JsonResponse($responseData, 201, [], true);
    }
}
