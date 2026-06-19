<?php

namespace App\Tests\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * Factory Foundry pour créer des User de test.
 *
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    /**
     * Mot de passe en clair correspondant au hash ci-dessous.
     * Utile si un test fait un vrai login via le formulaire.
     */
    public const string PLAIN_PASSWORD = 'password';

    public static function class(): string
    {
        return User::class;
    }

    /**
     * Tous les champs NON-NULLABLES de User doivent avoir une valeur par défaut,
     * sinon l'insert échoue : pseudo, email, password, roles, createdAt, enabled.
     *
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'pseudo' => self::faker()->unique()->userName(),
            'email' => self::faker()->unique()->safeEmail(),
            // Hash bcrypt (cost 4) de self::PLAIN_PASSWORD.
            'password' => '$2y$04$kvRLrghUFjx6JXH86e4yUOpffTwA0iljywUuqVB.HNJXwzfaCHyBO',
            'roles' => [],
            'createdAt' => new \DateTimeImmutable(),
            'enabled' => true,
        ];
    }
}
