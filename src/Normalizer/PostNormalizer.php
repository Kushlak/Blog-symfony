<?php

namespace App\Normalizer;

use App\Entity\Post;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class PostNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof Post) {
            throw new InvalidArgumentException('The object must be an instance of "App\Entity\Post".');
        }

        return [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'content' => $object->getContent(),
            'createdAt' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
            'type' => $object->getType()->value,
            'author' => $this->normalizeAuthor($object),
            'comments' => $this->normalizeComments($object),
        ];
    }

    private function normalizeAuthor(Post $post): array
    {
        $author = $post->getAuthor();

        return [
            'id' => $author->getId(),
            'username' => $author->getUsername(),
            'firstName' => $author->getFirstName(),
            'lastName' => $author->getLastName(),
        ];
    }

    private function normalizeComments(Post $post): array
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

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Post;
    }
}
