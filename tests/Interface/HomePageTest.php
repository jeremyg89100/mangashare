<?php

namespace App\Tests\Interface;

use Symfony\Component\Panther\PantherTestCase;

/**
 * Test d'INTERFACE : Panther lance un vrai serveur web + un navigateur Chromium
 * (headless) et charge réellement la page comme le ferait un utilisateur.
 *
 * ATTENTION : Panther tourne dans un PROCESS SÉPARÉ. dama/doctrine-test-bundle
 * (rollback transactionnel) ne s'applique donc PAS au serveur : les données créées
 * via Foundry dans le test ne seraient pas visibles par le navigateur.
 * On se limite ici à des assertions sur le contenu STATIQUE de la page (pas de DB).
 * Pour tester une page qui dépend de données, il faut les "commit" puis nettoyer
 * manuellement en tearDown.
 */
final class HomePageTest extends PantherTestCase
{
    public function testHomePageLoadsInRealBrowser(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        self::assertSelectorTextContains('h1', 'Partages avec toute une communauté');
        // Visiteur anonyme => le bouton de connexion est présent dans la navigation.
        self::assertSelectorExists('.nav-login-btn');
    }
}
