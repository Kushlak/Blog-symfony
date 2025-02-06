<?php
// src/Formatter/PostFormatter.php

namespace App\Formatter;

use App\Entity\Post;

class PostFormatter
{
    public function format(Post $post): array
    {
        return [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            'type' => $post->getType()->value,
            'author' => $this->formatAuthor($post),
            'comments' => $this->formatComments($post),
        ];
    }

    private function formatAuthor(Post $post): array
    {
        $author = $post->getAuthor();

        return [
            'id' => $author->getId(),
            'username' => $author->getUsername(),
            'firstName' => $author->getFirstName(),
            'lastName' => $author->getLastName(),
        ];
    }

    private function formatComments(Post $post): array
    {
        $comments = [];

        foreach ($post->getComments() as $comment) {
            $comments[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'author' => [
                    'id' => $comment->getAuthor()->getId(),
                    'username' => $comment->getAuthor()->getUsername(),
                ],
            ];
        }

        return $comments;
    }
}
