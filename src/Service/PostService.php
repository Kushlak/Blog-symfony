<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\PostKeyValueStore;
use App\Enum\CategoryType;
use App\Repository\PostRepository;
use App\Repository\PostKeyValueStoreRepository;
use App\Transformer\PostTransformer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostService
{
    private $postRepository;
    private $postKeyValueStoreRepository;
    private $postTransformer;

    public function __construct(PostRepository $postRepository, PostKeyValueStoreRepository $postKeyValueStoreRepository, PostTransformer $postTransformer)
    {
        $this->postRepository = $postRepository;
        $this->postKeyValueStoreRepository = $postKeyValueStoreRepository;
        $this->postTransformer = $postTransformer;
    }

    public function getAllPosts(): array
    {
        $posts = $this->postRepository->findAll();
        return array_map([$this->postTransformer, 'transform'], $posts);
    }

    public function getPostById(int $id): ?array
    {
        $post = $this->postRepository->find($id);

        return $post ? $this->postTransformer->transform($post) : null;
    }

    public function createPost(Request $request, User $user): array
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['content'], $data['type'])) {
            throw new \InvalidArgumentException('Invalid data');
        }

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setType(CategoryType::from($data['type']));
        $post->setAuthor($user);
        $post->setCreatedAt(new \DateTimeImmutable());

        $this->postRepository->save($post);

        return $this->postTransformer->transform($post);
    }

    public function updatePost(Request $request, Post $post): array
    {
        $data = json_decode($request->getContent(), true);

        $post = $this->postTransformer->reverseTransform($data, $post);
        $this->postRepository->save($post);

        return $this->postTransformer->transform($post);
    }

    public function deletePost(Post $post): void
    {
        $this->postRepository->remove($post);
    }

    public function addPostKeyValueStore(Post $post, Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['key'], $data['value'])) {
            throw new \InvalidArgumentException('Invalid data');
        }

        $postKeyValueStore = new PostKeyValueStore();
        $postKeyValueStore->setKey($data['key']);
        $postKeyValueStore->setValue($data['value']);
        $postKeyValueStore->setPost($post);

        $this->postKeyValueStoreRepository->save($postKeyValueStore);

        return $this->postTransformer->transform($post);
    }

    public function deletePostKeyValueStore(PostKeyValueStore $postKeyValueStore): void
    {
        $this->postKeyValueStoreRepository->remove($postKeyValueStore);
    }

    public function getPostKeyValueStore(PostKeyValueStore $postKeyValueStore): array
    {
        return $this->postTransformer->transform($postKeyValueStore);
    }

    public function getAllPostKeyValueStores(Post $post): array
    {
        $postKeyValueStores = $this->postKeyValueStoreRepository->findBy(['post' => $post]);
        return array_map([$this->postTransformer, 'transform'], $postKeyValueStores);
    }

    public function createPostWithForm(User $user, Post $post): void
    {
        $post->setAuthor($user);
        $post->setCreatedAt(new \DateTimeImmutable());

        $this->postRepository->save($post);
    }

    public function updatePostFromForm(Post $post): void
    {
        if ($post->getTitle() === null || trim($post->getTitle()) === '') {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        if ($post->getContent() === null || trim($post->getContent()) === '') {
            throw new \InvalidArgumentException('Content cannot be empty');
        }

        if ($post->getType() === null) {
            throw new \InvalidArgumentException('Type cannot be null');
        }

        $this->postRepository->save($post);
    }
}
