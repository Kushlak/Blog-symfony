<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostKeyValueStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostKeyValueStore>
 *
 * @method PostKeyValueStore|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostKeyValueStore|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostKeyValueStore[]    findAll()
 * @method PostKeyValueStore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostKeyValueStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostKeyValueStore::class);
    }

    /**
     * Save a PostKeyValueStore entity.
     *
     * @param PostKeyValueStore $entity
     * @param bool $flush
     */
    public function save(PostKeyValueStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a PostKeyValueStore entity.
     *
     * @param PostKeyValueStore $entity
     * @param bool $flush
     */
    public function remove(PostKeyValueStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Add your custom repository methods below

    /**
     * Find key-value pairs by post.
     *
     * @param Post $post
     * @return PostKeyValueStore[]
     */
    public function findByPost(Post $post): array
    {
        return $this->findBy(['post' => $post]);
    }

    /**
     * Find a key-value pair by post and key.
     *
     * @param Post $post
     * @param string $key
     * @return PostKeyValueStore|null
     */
    public function findOneByPostAndKey(Post $post, string $key): ?PostKeyValueStore
    {
        return $this->findOneBy(['post' => $post, 'key' => $key]);
    }
}
