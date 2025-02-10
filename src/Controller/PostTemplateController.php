<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostKeyValueStore;
use App\Service\PostService;
use App\Form\PostType;
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

    #[Route('/posts', name: 'posts_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->postService->createPostWithForm( $user, $post);
            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        }

        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/posts/{id}', name: 'posts_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postService->updatePostFromForm($post);
            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        }

        return $this->render('posts/update.html.twig', [
            'form' => $form->createView(),
        ]);
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
