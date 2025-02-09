<?php

namespace App\Tests\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Exception\CustomException;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\CommentService;
use App\Transformer\CommentTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    private $commentRepository;
    private $postRepository;
    private $entityManager;
    private $validator;
    private $commentTransformer;
    private CommentService $commentService;

    protected function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->commentTransformer = new CommentTransformer();

        $this->commentService = new CommentService(
            $this->commentRepository,
            $this->postRepository,
            $this->entityManager,
            $this->validator,
            $this->commentTransformer
        );
    }

    public function testGetCommentByIdFound(): void
    {
        $comment = new Comment();
        $comment->setContent('Test comment');

        $this->commentRepository
            ->expects($this->once())
            ->method('findCommentById')
            ->with(1)
            ->willReturn($comment);

        $result = $this->commentService->getComment(1);

        $this->assertNotNull($result);
        $this->assertEquals('Test comment', $result['content']);
    }

    public function testGetCommentByIdNotFound(): void
    {
        $this->commentRepository
            ->expects($this->once())
            ->method('findCommentById')
            ->with(1)
            ->willReturn(null);

        $result = $this->commentService->getComment(1);

        $this->assertNull($result);
    }

    public function testCreateCommentSuccess(): void
    {
        $user = new User();
        $post = new Post();

        $this->postRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($post);

        $this->commentRepository
            ->expects($this->once())
            ->method('save');

        $result = $this->commentService->createComment($user, 1, 'Test comment');

        $this->assertEquals('Test comment', $result['content']);
    }

    public function testCreateCommentPostNotFound(): void
    {
        $user = new User();

        $this->postRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('Post not found');

        $this->commentService->createComment($user, 1, 'Test comment');
    }
}
