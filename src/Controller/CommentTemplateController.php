<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentService;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentTemplateController extends AbstractController
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    #[Route('/posts/{postId}/comments', name: 'create_comment', methods: ['POST'])]
    public function createComment(Request $request, int $postId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->commentService->createComment($user, $postId, $comment->getContent());
            return $this->redirectToRoute('get_post_comments', ['postId' => $postId]);
        }

        return $this->render('comments/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comments/{id}', name: 'update_comment', methods: ['PUT', 'PATCH'])]
    public function updateComment(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comment = $this->commentService->getComment($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->commentService->updateComment($user, $id, ['content'=>$comment->getContent()]);
            return $this->redirectToRoute('get_comment', ['id' => $id]);
        }

        return $this->render('comments/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comments/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(Comment $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        try {
            $this->commentService->deleteComment($id);
            return $this->redirectToRoute('get_post_comments', ['postId' => $id->getPost()->getId()]);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
