<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Transformer\CommentTransformer;

class CommentService
{
    private CommentRepository $commentRepository;
    private CommentTransformer $commentTransformer;

    public function __construct(
        CommentRepository $commentRepository,
        CommentTransformer $commentTransformer
    ) {
        $this->commentRepository = $commentRepository;
        $this->commentTransformer = $commentTransformer;
    }

    public function saveComment(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    public function getComment(int $id): ?Comment
    {
        return $this->commentRepository->findCommentById($id);
    }

    public function getCommentsByPost(int $postId): array
    {
        return $this->commentRepository->findCommentsByPost($postId);
    }

    public function transformComment(Comment $comment): array
    {
        return $this->commentTransformer->transform($comment);
    }

    public function reverseTransformComment(array $data, Comment $comment): Comment
    {
        return $this->commentTransformer->reverseTransform($data, $comment);
    }
}
