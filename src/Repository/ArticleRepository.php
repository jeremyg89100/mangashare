<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findMostPopular(int $limit): array
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.likes', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->groupBy('article.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findMostPopularByCategory(string $category, int $limit): array
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.likes', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->where('article.category = :category')
            ->setParameter('category', $category)
            ->groupBy('article.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
