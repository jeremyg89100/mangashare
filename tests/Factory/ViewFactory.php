<?php

namespace App\Tests\Factory;

use App\Entity\View;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Factory Foundry pour créer des View (vues) de test.
 *
 * @extends PersistentObjectFactory<View>
 */
final class ViewFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return View::class;
    }

    /**
     * Côté entité, seul createdAt est NON-NULLABLE (manga/article/user sont optionnels).
     * Par défaut on crée une vue réaliste : un manga consulté par un utilisateur.
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'createdAt' => new \DateTimeImmutable(),
            'manga' => MangaFactory::new(),
            'user' => UserFactory::new(),
        ];
    }
}
