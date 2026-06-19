<?php

namespace App\Tests\Functional\Repository;

use App\Repository\ViewRepository;
use App\Tests\Factory\MangaFactory;
use App\Tests\Factory\ViewFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

/**
 * Test FONCTIONNEL d'intégration : on vérifie la logique du repository contre une
 * vraie base de données (filtrage par plage horaire d'une journée + type de contenu).
 */
final class ViewRepositoryTest extends KernelTestCase
{
    use Factories;

    private function getRepository(): ViewRepository
    {
        return self::getContainer()->get(ViewRepository::class);
    }

    public function testCountByDayCountsOnlyTheGivenDay(): void
    {
        self::bootKernel();

        $manga = MangaFactory::createOne();
        $day = new \DateTimeImmutable('2025-06-15 12:00:00');

        // 2 vues le jour ciblé, 1 vue la veille (ne doit pas être comptée).
        ViewFactory::createMany(2, ['manga' => $manga, 'createdAt' => $day]);
        ViewFactory::createOne(['manga' => $manga, 'createdAt' => $day->modify('-1 day')]);

        $mangaId = $manga->getId();
        self::assertNotNull($mangaId);

        $count = $this->getRepository()->countByDay($mangaId, 'manga', $day);

        self::assertSame(2, $count);
    }

    public function testCountByDayIgnoresOtherManga(): void
    {
        self::bootKernel();

        $manga = MangaFactory::createOne();
        $otherManga = MangaFactory::createOne();
        $day = new \DateTimeImmutable('2025-06-15 09:00:00');

        ViewFactory::createOne(['manga' => $manga, 'createdAt' => $day]);
        ViewFactory::createOne(['manga' => $otherManga, 'createdAt' => $day]);

        $mangaId = $manga->getId();
        self::assertNotNull($mangaId);

        $count = $this->getRepository()->countByDay($mangaId, 'manga', $day);

        self::assertSame(1, $count);
    }
}
