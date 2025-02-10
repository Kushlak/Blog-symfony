<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentService;
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

    #[Route('/posts/{postId}/comments', name: 'get_post_comments', methods: ['GET'])]
    public function getCommentsByPost(int $postId): Response
    {
        $comments = $this->commentService->getCommentsByPost($postId);
        return $this->render('comments/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/comments/{id}', name: 'get_comment', methods: ['GET'])]
    public function getComment(int $id): Response
    {
        $comment = $this->commentService->getComment($id);
        if (!$comment) {
            throw $this->createNotFoundException('Comment not found');
        }
        return $this->render('comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/posts/{postId}/comments', name: 'create_comment', methods: ['POST'])]
    public function createComment(Request $request, int $postId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data = $request->request->all();
        if (!isset($data['content'])) {
            return new Response('Content is required', Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getUser();
        $comment = $this->commentService->createComment($user, $postId, $data['content']);
        return $this->redirectToRoute('get_post_comments', ['postId' => $postId]);
    }
    #[Route('/comments/{id}', name: 'update_comment', methods: ['PUT', 'PATCH'])]
    public function updateComment(Request $request, int $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $data = $request->request->all();
        try {
            $comment = $this->commentService->updateComment($user, $id, $data);
            return $this->redirectToRoute('get_comment', ['id' => $id]);
        } catch (\InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
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
