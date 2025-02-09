<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Enum\CategoryType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            CategoryType::TECH,
            CategoryType::LIFESTYLE,
            CategoryType::NEWS,
            CategoryType::FOOD,
            CategoryType::HEALTH,
            CategoryType::FASHION,
            CategoryType::EDUCATION,
            CategoryType::TRAVEL,
            CategoryType::ENTERTAINMENT,
            CategoryType::BUSINESS,
            CategoryType::DIARY,
            CategoryType::BLOG,
            CategoryType::TUTORIAL,
        ];

        $postCount = 1;

        foreach ($categories as $category) {
            for ($i = 1; $i <= 10; $i++) {
                $post = new Post();
                $post->setTitle('Post Title ' . $postCount);
                $post->setContent('This is the content of post ' . $postCount);
                $post->setType($category);
                $post->setAuthor($this->getReference('user-' . ($postCount % 10 + 1)));
                $manager->persist($post);

                // Add a reference to use in other fixtures
                $this->addReference('post-' . $postCount, $post);

                $postCount++;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
