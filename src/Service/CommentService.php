<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CommentService
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(
        CommentRepository $commentRepository,
        PostRepository $postRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function getCommentsByPost(int $postId): array
    {
        return $this->commentRepository->findCommentsByPost($postId);
    }

    public function getComment(int $id): ?Comment
    {
        return $this->commentRepository->findCommentById($id);
    }

    public function saveComment(Comment $comment): void
    {
        $this->commentRepository->save($comment);
    }

    public function deleteComment(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    public function createComment(User $user, int $postId, string $content): Comment
    {
        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw new \InvalidArgumentException('Post not found');
        }

        $comment = new Comment();
        $comment->setContent($content);
        $comment->setAuthor($user);
        $comment->setPost($post);

        $this->saveComment($comment);

        return $comment;
    }

    public function updateComment(User $user, int $id, array $data): Comment
    {
        $comment = $this->commentRepository->find($id);
        if (!$comment) {
            throw new \InvalidArgumentException('Comment not found');
        }

        if ($comment->getAuthor()->getId() !== $user->getId()) {
            throw new \InvalidArgumentException('Unauthorized');
        }

        if (isset($data['content'])) {
            $comment->setContent($data['content']);
        }

        $this->saveComment($comment);

        return $comment;
    }
}
