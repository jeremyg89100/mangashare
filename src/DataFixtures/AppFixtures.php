<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Manga;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Create new test User
        $user = new User();
        $user->setPseudo('TestUser');
        $user->setEmail('test@test.com');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);

        $admin = new User();
        $admin->setPseudo('adminServer');
        $admin->setEmail('admin@test.fr');
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpassword45'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin);

        // Create tests Manga

        $titles = ['Naruto', 'One Piece', 'Dragon Ball', 'Bleach', 'Death Note'];
        foreach ($titles as $title) {
            $manga = new Manga();
            $manga->setTitle($title);
            $manga->setSynopsis('Synopsis of '.$title);
            $manga->setStatus('en cours');
            $manga->setCreatedAt(new \DateTimeImmutable());
            $manga->setReadingDirection('JP');
            $manga->setUser($user);
            $manga->setCategories(['Action']);
            $manager->persist($manga);
        }

        $titleArticlesDrawing = ['Jambes Anatomies Tuto', 'Bras Anatomies Tuto'];
        foreach ($titleArticlesDrawing as $titleArticleDrawing) {
            $article = new Article();
            $article->setTitle($titleArticleDrawing);
            $article->setTextContent('Ceci est un article de '.$titleArticleDrawing);
            $article->setCategory('Dessin & Anatomies');
            $article->setPublished(true);
            $article->setMiniature('astuces.png');
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUser($user);
            $manager->persist($article);
        }

        $titleArticlesStory = ['Scenario', 'Storytelling'];
        foreach ($titleArticlesStory as $titleArticleStory) {
            $article = new Article();
            $article->setTitle($titleArticleStory);
            $article->setTextContent('Ceci est un article de '.$titleArticleStory);
            $article->setCategory('Scenario & Storytelling');
            $article->setPublished(true);
            $article->setMiniature('astuces.png');
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUser($user);
            $manager->persist($article);
        }

        $titleArticlesSoftware = ['Clip Studio Paint', 'CSP'];
        foreach ($titleArticlesSoftware as $titleArticleSoftware) {
            $article = new Article();
            $article->setTitle($titleArticleSoftware);
            $article->setTextContent('Ceci est un article de '.$titleArticleSoftware);
            $article->setCategory('Techniques & Logiciels');
            $article->setPublished(true);
            $article->setMiniature('astuces.png');
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUser($user);
            $manager->persist($article);
        }

        $titleArticlesPublication = ['Clip Studio Paint', 'CSP'];
        foreach ($titleArticlesPublication as $titleArticlePublication) {
            $article = new Article();
            $article->setTitle($titleArticlePublication);
            $article->setTextContent('Ceci est un article de '.$titleArticlePublication);
            $article->setCategory('Edition & Publication');
            $article->setPublished(true);
            $article->setMiniature('astuces.png');
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setUser($user);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
