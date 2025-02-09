<?php

namespace App\Transformer;

use App\Entity\Post;

class PostTransformer
{
    /**
     * Transforms a Post entity into an associative array.
     *
     * @param Post $post
     * @return array
     */
    public function transform(Post $post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            'type' => $post->getType()->value,
            'author' => $post->getAuthor()->getId(),
            'comments' => array_map(fn($comment) => $comment->getId(), $post->getComments()->toArray()),
            'postKeyValueStores' => array_map(fn($store) => [
                'key' => $store->getKey(),
                'value' => $store->getValue()
            ], $post->getPostKeyValueStores()->toArray())
        ];
    }

    /**
     * Transforms an associative array into a Post entity.
     *
     * @param array $data
     * @param Post $post
     * @return Post
     */
    public function reverseTransform(array $data, Post $post): Post
    {
        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }

        if (isset($data['type'])) {
            $post->setType($data['type']);
        }

        return $post;
    }
}
