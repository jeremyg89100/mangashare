<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function countByDay(int $contentId, string $type, \DateTimeImmutable $date): int
    {
        $start = $date->setTime(0, 0, 0);
        $end = $date->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('comment')
            ->select('COUNT(comment.id)')
            ->andWhere('comment.createdAt >= :start')
            ->andWhere('comment.createdAt <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ('manga' === $type) {
            $qb->andWhere('comment.manga = :id')->setParameter('id', $contentId);
        } elseif ('article' === $type) {
            $qb->andWhere('comment.article = :id')->setParameter('id', $contentId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
