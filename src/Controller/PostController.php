<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostKeyValueStore;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[Route('/api/posts', name: 'api_posts_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $jsonData = $this->postService->getAllPosts();
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/posts/{id}', name: 'api_posts_show', methods: ['GET'])]
    public function show(Post $post): JsonResponse
    {
        $jsonData = $this->postService->getPostById($post);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/posts', name: 'api_posts_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $user = $this->getUser(); // Отримуємо поточного користувача
            if (!$user) {
                return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
            }

            $jsonData = $this->postService->createPost($request, $user); // Передаємо користувача в сервіс
            return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/posts/{id}', name: 'api_posts_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post): JsonResponse
    {
        $jsonData = $this->postService->updatePost($request, $post);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/posts/{id}', name: 'api_posts_delete', methods: ['DELETE'])]
    public function delete(Post $post): JsonResponse
    {
        $this->postService->deletePost($post);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/posts/{id}/key_value_stores', name: 'api_posts_add_key_value_store', methods: ['POST'])]
    public function addKeyValueStore(Request $request, Post $post): JsonResponse
    {
        try {
            $jsonData = $this->postService->addPostKeyValueStore($post, $request);
            return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/posts/{id}/key_value_stores', name: 'api_posts_get_key_value_stores', methods: ['GET'])]
    public function getKeyValueStores(Post $post): JsonResponse
    {
        $jsonData = $this->postService->getAllPostKeyValueStores($post);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/posts/{postId}/key_value_stores/{id}', name: 'api_posts_delete_key_value_store', methods: ['DELETE'])]
    public function deleteKeyValueStores(PostKeyValueStore $postKeyValueStore): JsonResponse
    {
        $this->postService->deletePostKeyValueStore($postKeyValueStore);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
