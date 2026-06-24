<?php

namespace App\Tests\Functional\Entity;

use App\Tests\Factory\CommentFactory;
use App\Tests\Factory\MangaFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CommentTest extends KernelTestCase
{
    use Factories; // ResetDatabase vide la base Postgres entre chaque scénario pour qu'elle reste propre
    use ResetDatabase;

    public function testCommentCreationAndRelations(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['pseudo' => 'TestCommentFactory']);
        $manga = MangaFactory::createOne(['title' => 'Chainsaw Man']);

        $comment = CommentFactory::createOne([
            'textContent' => 'Ce chapitre est incroyable !',
            'user' => $user,
            'manga' => $manga,
        ]);

        self::assertNotNull($comment->getId(), 'Le commentaire aurait dû être enregistré en base de données avec un ID.');
        self::assertSame('Ce chapitre est incroyable !', $comment->getTextContent());
        self::assertSame('TestCommentFactory', $comment->getUser()->getPseudo(), "Le commentaire n'est pas relié au bon utilisateur.");
        self::assertNotNull($comment->getManga());
        self::assertSame('Chainsaw Man', $comment->getManga()->getTitle(), "Le commentaire n'est pas lié au bon Manga.");
        self::assertFalse($comment->isReported(), 'Par défaut, un commentaire ne devrait pas être signalé.');
    }
}
