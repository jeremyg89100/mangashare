<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function countByDay(int $contentId, string $type, \DateTimeImmutable $date): int
    {
        $start = $date->setTime(0, 0, 0);
        $end = $date->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('userLike')
            ->select('COUNT(userLike.id)')
            ->andWhere('userLike.createdAt >= :start')
            ->andWhere('userLike.createdAt <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ('manga' === $type) {
            $qb->andWhere('userLike.manga = :id')->setParameter('id', $contentId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
