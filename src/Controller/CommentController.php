<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CommentController extends AbstractController
{
    private CommentService $commentService;
    private SerializerInterface $serializer;

    public function __construct(
        CommentService      $commentService,
        SerializerInterface $serializer
    )
    {
        $this->commentService = $commentService;
        $this->serializer = $serializer;
    }

    #[Route('/api/posts/{postId}/comments', name: 'api_get_post_comments', methods: ['GET'])]
    public function getCommentsByPost(int $postId): JsonResponse
    {
        $comments = $this->commentService->getCommentsByPost($postId);
        $jsonContent = $this->serializer->serialize($comments, 'json', ['groups' => 'comment:read']);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/api/comments/{id}', name: 'api_get_comment', methods: ['GET'])]
    public function getComment(int $id): JsonResponse
    {
        $comment = $this->commentService->getComment($id);
        if (!$comment) {
            return new JsonResponse(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }
        $jsonContent = $this->serializer->serialize($comment, 'json', ['groups' => 'comment:read']);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/api/posts/{postId}/comments', name: 'api_create_comment', methods: ['POST'])]
    public function createComment(int $postId, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data = json_decode($request->getContent(), true);
        if (!isset($data['content'])) {
            return new JsonResponse(['message' => 'Content is required'], Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getUser();
        $comment = $this->commentService->createComment($user, $postId, $data['content']);
        $jsonContent = $this->serializer->serialize($comment, 'json', ['groups' => 'comment:read']);
        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/comments/{id}', name: 'api_update_comment', methods: ['PUT', 'PATCH'])]
    public function updateComment(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        try {
            $comment = $this->commentService->updateComment($user, $id, $data);
            $jsonContent = $this->serializer->serialize($comment, 'json', ['groups' => 'comment:read']);
            return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('/api/comments/{id}', name: 'api_delete_comment', methods: ['DELETE'])]
    public function deleteComment(Comment $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        try {
            $this->commentService->deleteComment( $id);
            return new JsonResponse(['message' => 'Comment deleted'], Response::HTTP_NO_CONTENT);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode());
        }
    }
}
