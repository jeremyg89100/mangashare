<?php

namespace App\Tests\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Factory Foundry pour créer des Comment de test.
 *
 * @extends PersistentObjectFactory<Comment>
 */
final class CommentFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Comment::class;
    }

    /**
     * Champs NON-NULLABLES de Comment : textContent, createdAt, user, isReported.
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'textContent' => self::faker()->paragraph(),
            'createdAt' => new \DateTimeImmutable(),
            'user' => UserFactory::new(),
            'manga' => MangaFactory::new(),
            'isReported' => false,
        ];
    }
}
