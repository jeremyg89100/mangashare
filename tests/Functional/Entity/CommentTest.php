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
    // ResetDatabase vide la base Postgres entre chaque scénario pour qu'elle reste propre
    use ResetDatabase, Factories;

    public function testCommentCreationAndRelations(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['pseudo' => 'TestCommentFactory']);
        $manga = MangaFactory::createOne(['title' => 'Chainsaw Man']);

        $comment = CommentFactory::createOne([
            'textContent' => 'Ce chapitre est incroyable !',
            'user' => $user,
            'manga' => $manga
        ]);

        $this->assertNotNull($comment->getId(), "Le commentaire aurait dû être enregistré en base de données avec un ID.");
        $this->assertSame('Ce chapitre est incroyable !', $comment->getTextContent());
        $this->assertSame('TestCommentFactory', $comment->getUser()->getPseudo(), "Le commentaire n'est pas relié au bon utilisateur.");
        $this->assertSame('Chainsaw Man', $comment->getManga()->getTitle(), "Le commentaire n'est pas lié au bon Manga.");
        $this->assertFalse($comment->isReported(), "Par défaut, un commentaire ne devrait pas être signalé.");
    }
}
