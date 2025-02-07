<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\PostKeyValueStore;
use App\Enum\CategoryType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostService
{
    private $entityManager;
    private $postRepository;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, PostRepository $postRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
        $this->serializer = $serializer;
    }

    public function getAllPosts(): string
    {
        $posts = $this->postRepository->findAll();
        return $this->serializer->serialize($posts, 'json');
    }

    public function getPostById(Post $post): string
    {
        return $this->serializer->serialize($post, 'json');
    }

    public function createPost(Request $request): string
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['content'], $data['type'])) {
            throw new \InvalidArgumentException('Invalid data');
        }

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setType(CategoryType::from($data['type']));
        //пошукати інфу щодо гет юзер
        $post->setAuthor($request->getUser());
        $post->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->serializer->serialize($post, 'json');
    }

    public function updatePost(Request $request, Post $post): string
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title'] ?? $post->getTitle());
        $post->setContent($data['content'] ?? $post->getContent());
        if (isset($data['type'])) {
            $post->setType(CategoryType::from($data['type']));
        }

        $this->entityManager->flush();

        return $this->serializer->serialize($post, 'json');
    }

    public function deletePost(Post $post)
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    public function addPostKeyValueStore(Post $post, Request $request): string
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['key'], $data['value'])) {
            throw new \InvalidArgumentException('Invalid data');
        }

        $postKeyValueStore = new PostKeyValueStore();
        $postKeyValueStore->setKey($data['key']);
        $postKeyValueStore->setValue($data['value']);
        $postKeyValueStore->setPost($post);

        $this->entityManager->persist($postKeyValueStore);
        $this->entityManager->flush();

        return $this->serializer->serialize($postKeyValueStore, 'json');
    }

    public function deletePostKeyValueStore(PostKeyValueStore $postKeyValueStore)
    {
        $this->entityManager->remove($postKeyValueStore);
        $this->entityManager->flush();
    }

    public function getPostKeyValueStore(PostKeyValueStore $postKeyValueStore): string
    {
        return $this->serializer->serialize($postKeyValueStore, 'json');
    }

    public function getAllPostKeyValueStores(Post $post): string
    {
        $postKeyValueStores = $post->getPostKeyValueStores(); // Assuming you have a method in Post entity to get the stores
        return $this->serializer->serialize($postKeyValueStores, 'json');
    }
}
