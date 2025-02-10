<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Transformer\CommentTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentService
{
    private CommentRepository $commentRepository;
    private CommentTransformer $commentTransformer;

    public function __construct(CommentRepository $commentRepository, CommentTransformer $commentTransformer)
    {
        $this->commentRepository = $commentRepository;
        $this->commentTransformer = $commentTransformer;
    }

    public function getCommentsByPost(int $postId): array
    {
        $comments = $this->commentRepository->findCommentsByPost($postId);

        return array_map([$this->commentTransformer, 'transform'], $comments);
    }

    public function getComment(int $id): ?array
    {
        $comment = $this->commentRepository->findCommentById($id);

        return $comment ? $this->commentTransformer->transform($comment) : null;
    }

    public function createComment(UserInterface $user, int $postId, string $content): array
    {
        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setPost($postId); // Assuming $postId is a valid Post entity.
        $comment->setContent($content);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $this->commentRepository->save($comment);

        return $this->commentTransformer->transform($comment);
    }

    public function updateComment(UserInterface $user, int $id, array $data): array
    {
        $comment = $this->commentRepository->findCommentById($id);

        if (!$comment) {
            throw new \InvalidArgumentException('Comment not found');
        }

        $comment = $this->commentTransformer->reverseTransform($data, $comment);

        $this->commentRepository->save($comment);

        return $this->commentTransformer->transform($comment);
    }

    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }
}
