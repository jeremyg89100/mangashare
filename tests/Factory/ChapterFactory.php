<?php

namespace App\Tests\Factory;

use App\Entity\Chapter;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Factory Foundry pour créer des Chapter de test.
 *
 * @extends PersistentObjectFactory<Chapter>
 */
final class ChapterFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Chapter::class;
    }

    /**
     * Champs NON-NULLABLES de Chapter : title, number, published, createdAt, manga.
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->sentence(2),
            'number' => self::faker()->numberBetween(1, 50),
            'published' => true,
            'createdAt' => new \DateTimeImmutable(),
            'manga' => MangaFactory::new(),
        ];
    }
}
