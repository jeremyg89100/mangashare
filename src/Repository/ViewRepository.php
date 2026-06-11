<?php

namespace App\Repository;

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
            $qb->andWhere('view.manga = :id');
        }

        $qb->setParameter('id', $contentId);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    //    /**
    //     * @return View[] Returns an array of View objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?View
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
