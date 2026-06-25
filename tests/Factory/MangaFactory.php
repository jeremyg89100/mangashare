<?php

namespace App\Tests\Factory;

use App\Entity\Manga;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Factory Foundry pour créer des Manga de test.
 *
 * @extends PersistentObjectFactory<Manga>
 */
final class MangaFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Manga::class;
    }

    /**
     * Champs NON-NULLABLES de Manga : title, readingDirection, status,
     * createdAt et la relation user (JoinColumn nullable: false).
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'title' => self::faker()->unique()->sentence(3),
            'synopsis' => self::faker()->paragraph(),
            'readingDirection' => 'ltr',
            'status' => 'ongoing',
            'categories' => [],
            'createdAt' => new \DateTimeImmutable(),
            // Une factory en valeur => Foundry crée le User lié automatiquement.
            'user' => UserFactory::new(),
        ];
    }
}
