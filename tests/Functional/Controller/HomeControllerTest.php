<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\MangaFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * Test FONCTIONNEL : on simule une requête HTTP sur la page d'accueil et on
 * inspecte la réponse (code 200, contenu du HTML rendu).
 *
 * Le trait Factories permet d'utiliser Foundry. L'isolation de la base entre
 * les tests est assurée par dama/doctrine-test-bundle (rollback automatique).
 */
final class HomeControllerTest extends WebTestCase
{
    use Factories;

    public function testHomePageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Partages avec toute une communauté');
    }

    public function testHomePageDisplaysCreatedManga(): void
    {
        // createClient() doit booter le kernel AVANT que Foundry persiste.
        $client = static::createClient();

        MangaFactory::createOne(['title' => 'Mon Manga De Test']);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.manga-grid', 'Mon Manga De Test');
    }
}
