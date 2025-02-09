<?php
namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setFirstName('FirstName' . $i);
            $user->setLastName('LastName' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $password = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($password);
            $manager->persist($user);
$this->addReference('user-' . $i, $user);
}
        $manager->flush();
    }
}
