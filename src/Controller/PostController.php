<?php

namespace App\Controller;

use App\Entity\Post;
use App\Enum\CategoryType;
use App\Repository\PostRяяepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface; // Додайте це
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
    // Отримати всі пости
    #[Route('/api/posts', name: 'api_posts_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, SerializerInterface $serializer): JsonResponse
    {
        $posts = $postRepository->findAll();

        // Серіалізуємо пости
        $jsonData = $serializer->serialize($posts, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    // Показати один пост
    #[Route('/api/posts/{id}', name: 'api_posts_show', methods: ['GET'])]
    public function show(Post $post, SerializerInterface $serializer): JsonResponse
    {
        $jsonData = $serializer->serialize($post, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    // Створити новий пост
    #[Route('/api/posts', name: 'api_posts_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['content'], $data['type'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setType(CategoryType::from($data['type']));
        $post->setAuthor($this->getUser());
        $post->setCreatedAt(new \DateTimeImmutable());

        $em->persist($post);
        $em->flush();

        $jsonData = $serializer->serialize($post, 'json');


        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    // Оновити існуючий пост
    #[Route('/api/posts/{id}', name: 'api_posts_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title'] ?? $post->getTitle());
        $post->setContent($data['content'] ?? $post->getContent());
        if (isset($data['type'])) {
            $post->setType(CategoryType::from($data['type']));
        }

        $em->flush();

        $jsonData = $serializer->serialize($post, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    // Видалити пост
    #[Route('/api/posts/{id}', name: 'api_posts_delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

