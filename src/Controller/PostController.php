<?php

namespace App\Controller;

use App\Entity\Post;
use App\Enum\CategoryType;
use App\Formatter\PostFormatter;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
// List all posts
    #[Route('/api/posts', name: 'api_posts_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PostFormatter $postFormatter): JsonResponse
    {
        $posts = $postRepository->findAll();

        $formattedPosts = array_map(
            fn(Post $post) => $postFormatter->format($post),
            $posts
        );

        return new JsonResponse($formattedPosts, Response::HTTP_OK);
    }

// Show a single post
    #[Route('/api/posts/{id}', name: 'api_posts_show', methods: ['GET'])]
    public function show(Post $post, PostFormatter $postFormatter): JsonResponse
    {
        $formattedPost = $postFormatter->format($post);

        return new JsonResponse($formattedPost, Response::HTTP_OK);
    }

// Create a new post
    #[Route('/api/posts', name: 'api_posts_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, PostFormatter $postFormatter): JsonResponse
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

        $formattedPost = $postFormatter->format($post);

        return new JsonResponse($formattedPost, Response::HTTP_CREATED);
    }

// Update an existing post
    #[Route('/api/posts/{id}', name: 'api_posts_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post, EntityManagerInterface $em, PostFormatter $postFormatter): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title'] ?? $post->getTitle());
        $post->setContent($data['content'] ?? $post->getContent());
        if (isset($data['type'])) {
            $post->setType(CategoryType::from($data['type']));
        }

        $em->flush();

        $formattedPost = $postFormatter->format($post);

        return new JsonResponse($formattedPost, Response::HTTP_OK);
    }

// Delete a post
    #[Route('/api/posts/{id}', name: 'api_posts_delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
