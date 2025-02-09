<?php

namespace App\Tests\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Exception\CustomException;
use App\Repository\PostRepository;
use App\Service\PostService;
use App\Transformer\PostTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PostServiceTest extends TestCase
{
    private $entityManager;
    private $postRepository;
    private $serializer;
    private $postTransformer;
    private PostService $postService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->postTransformer = new PostTransformer();

        $this->postService = new PostService(
            $this->entityManager,
            $this->postRepository,
            $this->serializer,
            $this->postTransformer
        );
    }

    public function testGetPostByIdFound(): void
    {
        $post = new Post();
        $post->setTitle('Test title');

        $this->postRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($post);

        $result = $this->postService->getPostById($post);

        $this->assertNotNull($result);
        $this->assertEquals('Test title', $result['title']);
    }

    public function testCreatePostSuccess(): void
    {
        $user = new User();
        $request = new Request([], [], [], [], [], [], json_encode([
            'title' => 'Test title',
            'content' => 'Test content',
            'type' => 'lifestyle'
        ]));

        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->postService->createPost($request, $user);

        $this->assertEquals('Test title', $result['title']);
    }

    public function testCreatePostInvalidData(): void
    {
        $user = new User();
        $request = new Request([], [], [], [], [], [], json_encode([
            'title' => 'Test title'
        ]));

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('Invalid data');

        $this->postService->createPost($request, $user);
    }
}
