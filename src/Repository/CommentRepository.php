<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    public function delete(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($comment);
        $entityManager->flush();
    }

    /**
     * Find a comment by its ID.
     *
     * @param int $id
     * @return Comment|null
     */
    public function findCommentById(int $id): ?Comment
    {
        return $this->find($id);
    }

    /**
     * Find comments by post ID.
     *
     * @param int $postId
     * @return Comment[]
     */
    public function findCommentsByPost(int $postId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.post = :postId')
            ->setParameter('postId', $postId)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
