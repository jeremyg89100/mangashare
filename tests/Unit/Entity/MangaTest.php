<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Like;
use App\Entity\Manga;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test UNITAIRE : logique pure de l'entité Manga, sans base de données ni kernel.
 *
 * On instancie les objets à la main et on vérifie le comportement de la méthode
 * Manga::isLikedByUser(). Aucune I/O => test très rapide.
 */
final class MangaTest extends TestCase
{
    public function testIsLikedByUserReturnsFalseWhenUserIsNull(): void
    {
        $manga = new Manga();

        self::assertFalse($manga->isLikedByUser(null));
    }

    public function testIsLikedByUserReturnsTrueWhenUserHasLiked(): void
    {
        $user = new User();
        $manga = new Manga();

        $like = new Like()->setUser($user);
        $manga->addLike($like);

        self::assertTrue($manga->isLikedByUser($user));
    }

    public function testIsLikedByUserReturnsFalseForAnotherUser(): void
    {
        $author = new User();
        $otherUser = new User();
        $manga = new Manga();

        $like = new Like()->setUser($author);
        $manga->addLike($like);

        self::assertFalse($manga->isLikedByUser($otherUser));
    }
}
