<?php
namespace App\DataFixtures;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $comment = new Comment();
            $comment->setContent('This is a comment ' . $i);
            $comment->setAuthor($this->getReference('user-' . ($i % 10 + 1)));
            $comment->setPost($this->getReference('post-' . ($i % 10 + 1)));
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            PostFixture::class,
        ];
    }
}
