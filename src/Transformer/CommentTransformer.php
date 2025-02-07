<?php

namespace App\Transformer;

use App\Entity\Comment;

class CommentTransformer
{
    /**
     * Transforms a Comment entity into an associative array.
     *
     * @param Comment $comment
     * @return array
     */
    public function transform(Comment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            'author' => $comment->getAuthor()->getId(),
            'post' => $comment->getPost()->getId(),
        ];
    }

    /**
     * Transforms an associative array into a Comment entity.
     *
     * @param array $data
     * @param Comment $comment
     * @return Comment
     */
    public function reverseTransform(array $data, Comment $comment): Comment
    {
        if (isset($data['content'])) {
            $comment->setContent($data['content']);
        }

        return $comment;
    }
}
