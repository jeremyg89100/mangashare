<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
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

    /**
     * @return Article[]
     */
    public function findMostPopular(int $limit): array
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.likeArticles', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->groupBy('article.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findMostPopularByCategory(string $category, int $limit): array
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.likeArticles', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->where('article.category = :category')
            ->setParameter('category', $category)
            ->groupBy('article.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findByUser(User $user, int $limit = 12): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[]
     */
    public function findByUserAndCategory(User $user, string $category, int $limit = 12): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.category = :category')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, Article>
     */
    public function search(string $query, int $maxResult): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.title LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }
}
