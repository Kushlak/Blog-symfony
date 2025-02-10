<?php

namespace App\Repository;

use App\Entity\PostKeyValueStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

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
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, PostKeyValueStore::class);
        $this->entityManager = $entityManager;
    }

    public function save(PostKeyValueStore $postKeyValueStore): void
    {
        $this->entityManager->persist($postKeyValueStore);
        $this->entityManager->flush();
    }

    public function remove(PostKeyValueStore $postKeyValueStore): void
    {
        $this->entityManager->remove($postKeyValueStore);
        $this->entityManager->flush();
    }
}
