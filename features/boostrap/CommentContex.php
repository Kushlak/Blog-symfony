<?php

namespace App\Tests\FeatureContext;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\CommentService;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Transformer\CommentTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class CommentContext implements Context
{
    private $commentService;
    private $commentRepository;
    private $postRepository;
    private $entityManager;
    private $validator;
    private $commentTransformer;
    private $comment;
    private $lastException;
    private $fetchedComment;
    private $fetchedComments;
    private $post;

    public function __construct()
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

    /**
     * @Given a new comment with content :content
     */
    public function aNewCommentWithContent($content)
    {
        $this->comment = new Comment();
        $this->comment->setContent($content);
    }

    /**
     * @Given a new comment with invalid content
     */
    public function aNewCommentWithInvalidContent()
    {
        $this->comment = new Comment();
        $this->comment->setContent(''); // Invalid content
    }

    /**
     * @When I save the comment
     */
    public function iSaveTheComment()
    {
        try {
            $this->commentService->saveComment($this->comment);
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then the comment should be saved successfully
     */
    public function theCommentShouldBeSavedSuccessfully()
    {
        Assert::assertNull($this->lastException);
    }

    /**
     * @Then I should receive a validation error
     */
    public function iShouldReceiveAValidationError()
    {
        Assert::assertInstanceOf(CustomException::class, $this->lastException);
        Assert::assertStringContainsString('Invalid data', $this->lastException->getMessage());
    }

    /**
     * @Given an existing comment
     */
    public function anExistingComment()
    {
        $this->comment = new Comment();
        $this->comment->setContent('Existing comment');
        $this->entityManager->persist($this->comment);
        $this->entityManager->flush();
    }

    /**
     * @When I delete the comment
     */
    public function iDeleteTheComment()
    {
        try {
            $this->commentService->deleteComment($this->comment);
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then the comment should be deleted successfully
     */
    public function theCommentShouldBeDeletedSuccessfully()
    {
        Assert::assertNull($this->lastException);
        Assert::assertNull($this->commentRepository->findCommentById($this->comment->getId()));
    }

    /**
     * @Given an existing comment with ID :id
     */
    public function anExistingCommentWithId($id)
    {
        $this->comment = new Comment();
        $this->comment->setId($id);
        $this->comment->setContent('Comment with ID');
        $this->entityManager->persist($this->comment);
        $this->entityManager->flush();
    }

    /**
     * @When I fetch the comment by ID
     */
    public function iFetchTheCommentById()
    {
        try {
            $this->fetchedComment = $this->commentService->getComment($this->comment->getId());
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then I should receive the comment details
     */
    public function iShouldReceiveTheCommentDetails()
    {
        Assert::assertNull($this->lastException);
        Assert::assertEquals($this->comment->getContent(), $this->fetchedComment['content']);
    }

    /**
     * @Given an existing post with comments
     */
    public function anExistingPostWithComments()
    {
        $post = new Post();
        $post->setTitle('Post with comments');
        $this->entityManager->persist($post);

        $comment = new Comment();
        $comment->setContent('Comment for post');
        $comment->setPost($post);
        $this->entityManager->persist($comment);

        $this->entityManager->flush();
        $this->post = $post;
    }

    /**
     * @When I fetch comments by post ID
     */
    public function iFetchCommentsByPostId()
    {
        try {
            $this->fetchedComments = $this->commentService->getCommentsByPost($this->post->getId());
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @Then I should receive the list of comments
     */
    public function iShouldReceiveTheListOfComments()
    {
        Assert::assertNull($this->lastException);
        Assert::assertNotEmpty($this->fetchedComments);
    }
}
