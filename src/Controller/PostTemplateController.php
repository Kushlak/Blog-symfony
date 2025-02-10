<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostKeyValueStore;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostTemplateController extends AbstractController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[Route('/posts', name: 'posts_index', methods: ['GET'])]
    public function index(): Response
    {
        $posts = $this->postService->getAllPosts();
        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/posts/{id}', name: 'posts_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('posts/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/posts', name: 'posts_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $user = $this->getUser();
            if (!$user) {
                return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
            }
            $post = $this->postService->createPost($request, $user);
            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/posts/{id}', name: 'posts_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post): Response
    {
        $updatedPost = $this->postService->updatePost($request, $post);
        return $this->redirectToRoute('posts_show', ['id' => $updatedPost->getId()]);
    }

    #[Route('/posts/{id}', name: 'posts_delete', methods: ['DELETE'])]
    public function delete(Post $post): Response
    {
        $this->postService->deletePost($post);
        return $this->redirectToRoute('posts_index');
    }

    #[Route('/posts/{id}/key_value_stores', name: 'posts_add_key_value_store', methods: ['POST'])]
    public function addKeyValueStore(Request $request, Post $post): Response
    {
        try {
            $keyValueStore = $this->postService->addPostKeyValueStore($post, $request);
            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/posts/{id}/key_value_stores', name: 'posts_get_key_value_stores', methods: ['GET'])]
    public function getKeyValueStores(Post $post): Response
    {
        $keyValueStores = $this->postService->getAllPostKeyValueStores($post);
        return $this->render('posts/key_value_stores.html.twig', [
            'key_value_stores' => $keyValueStores,
        ]);
    }

    #[Route('/posts/{postId}/key_value_stores/{id}', name: 'posts_delete_key_value_store', methods: ['DELETE'])]
    public function deleteKeyValueStore(PostKeyValueStore $postKeyValueStore): Response
    {
        $this->postService->deletePostKeyValueStore($postKeyValueStore);
        return $this->redirectToRoute('posts_show', ['id' => $postKeyValueStore->getPost()->getId()]);
    }
}
