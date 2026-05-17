<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use App\Entity\User;
use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Create new test User
        $user = new User();
        $user->setPseudo('TestUser');
        $user->setEmail('test@test.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setRoles([]);
        $user->setCreatedAt(new \DateTime());
        $manager->persist($user);

        // Create tests Manga

        $titles = ['Naruto', 'One Piece', 'Dragon Ball', 'Bleach', 'Death Note'];
        foreach ($titles as $title) {
            $manga = new Manga();
            $manga->setTitle($title);
            $manga->setSynopsis('Synopsis of ' . $title);
            $manga->setStatus('en cours');
            $manga->setCreatedAt(new \DateTime());
            $manga->setReadingDirection('JP');
            $manga->setViews(rand(0,1000));
            $manga->setUser($user);
            $manager->persist($manga);
        }

        $manager->flush();
    }
}
