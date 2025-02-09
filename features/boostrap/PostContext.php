<?php

namespace App\Tests\FeatureContext;

use App\Entity\Post;
use App\Entity\User;
use App\Service\PostService;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PostContext implements Context
{
    private $postService;
    private $postRepository;
    private $entityManager;
    private $validator;
    private $post;
    private $lastException;
    private $fetchedPost;
    private $fetchedPosts;
    private $post1;
    private $post2;

    public function __construct()
    {
        $this->postRepository = $this->createMock(PostRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->postService = new PostService(
            $this->entityManager,
            $this->postRepository,
            $this->createMock(SerializerInterface::class)
        );
    }

    /**
     * @Given a new post with title :title and content :content by user :username
     */
    public function aNewPostWithTitleAndContentByUser($title, $content, $username)
    {
        $user = new User();
        $user->setUsername($username);

        $this->post = new Post();
        $this->post->setTitle($title);
        $this->post->setContent($content);
        $this->post->setAuthor($user);
    }

    /**
     * @When I save the post
     */
    public function iSaveThePost()
    {
        $request = new Request([], [], [], [], [], [], json_encode([
            'title' => $this->post->getTitle(),
            'content' => $this->post->getContent(),
            'type' => 'blog',
        ]));

        try {
            $this->postService->createPost($request, $this->post->getAuthor());
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then the post should be saved successfully
     */
    public function thePostShouldBeSavedSuccessfully()
    {
        Assert::assertNull($this->lastException);
    }

    /**
     * @Given an existing post with title :title and content :content
     */
    public function anExistingPostWithTitleAndContent($title, $content)
    {
        $this->post = new Post();
        $this->post->setTitle($title);
        $this->post->setContent($content);
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    /**
     * @When I update the post with title :title and content :content
     */
    public function iUpdateThePostWithTitleAndContent($title, $content)
    {
        $request = new Request([], [], [], [], [], [], json_encode([
            'title' => $title,
            'content' => $content,
        ]));

        try {
            $this->postService->updatePost($request, $this->post);
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then the post should be updated successfully
     */
    public function thePostShouldBeUpdatedSuccessfully()
    {
        Assert::assertNull($this->lastException);
    }

    /**
     * @Given an existing post with ID :id
     */
    public function anExistingPostWithId($id)
    {
        $this->post = new Post();
        $this->post->setId($id);
        $this->post->setTitle('Test Post');
        $this->post->setContent('Test Content');
        $this->entityManager->persist($this->post);
        $this->entityManager->flush();
    }

    /**
     * @When I fetch the post by ID
     */
    public function iFetchThePostById()
    {
        try {
            $this->fetchedPost = $this->postService->getPostById($this->post);
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then I should receive the post details
     */
    public function iShouldReceiveThePostDetails()
    {
        Assert::assertNull($this->lastException);
        Assert::assertEquals($this->post->getTitle(), json_decode($this->fetchedPost, true)['title']);
    }

    /**
     * @Given there are existing posts
     */
    public function thereAreExistingPosts()
    {
        $this->post1 = new Post();
        $this->post1->setTitle('First Post');
        $this->post1->setContent('Content of the first post');
        $this->entityManager->persist($this->post1);

        $this->post2 = new Post();
        $this->post2->setTitle('Second Post');
        $this->post2->setContent('Content of the second post');
        $this->entityManager->persist($this->post2);

        $this->entityManager->flush();
    }

    /**
     * @When I fetch all posts
     */
    public function iFetchAllPosts()
    {
        try {
            $this->fetchedPosts = $this->postService->getAllPosts();
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then I should receive the list of posts
     */
    public function iShouldReceiveTheListOfPosts()
    {
        Assert::assertNull($this->lastException);
        $posts = json_decode($this->fetchedPosts, true);
        Assert::assertCount(2, $posts);
    }
}
