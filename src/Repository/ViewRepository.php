<?php

namespace App\Repository;

use App\Entity\Manga;
use App\Entity\User;
use App\Entity\View;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<View>
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }

    public function countByDay(int $contentId, string $type, \DateTimeImmutable $date): int
    {
        $start = $date->setTime(0, 0, 0);
        $end = $date->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('view')
            ->select('COUNT(view.id)')
            ->andWhere('view.createdAt >= :start')
            ->andWhere('view.createdAt <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ('manga' === $type) {
            $qb->andWhere('view.manga = :id')->setParameter('id', $contentId);
        } elseif ('article' === $type) {
            $qb->andWhere('view.article = :id')->setParameter('id', $contentId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function hasViewedMangaOnDay(User $user, Manga $manga, \DateTimeImmutable $date): bool
    {
        $start = $date->setTime(0, 0, 0);
        $end = $date->setTime(23, 59, 59);

        $count = (int) $this->createQueryBuilder('view')
            ->select('COUNT(view.id)')
            ->andWhere('view.user = :user')
            ->andWhere('view.manga = :manga')
            ->andWhere('view.createdAt >= :start')
            ->andWhere('view.createdAt <= :end')
            ->setParameter('user', $user)
            ->setParameter('manga', $manga)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
