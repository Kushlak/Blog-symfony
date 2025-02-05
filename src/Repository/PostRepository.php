<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }









/* find posts by their type
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

// find latest posts
public function findLatestPosts(int $limit = 10): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult()
    ;
}
//find posts by author
public function findByAuthor(int $authorId): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.author = :authorId')
        ->setParameter('authorId', $authorId)
        ->orderBy('p.createdAt', 'DESC')
        ->getQuery()
        ->getResult()
    ;
}


*/
    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
