<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    /**
     * @return array<int, Manga>
     */
    public function findMostPopular(int $limit): array
    {
        return $this->createQueryBuilder('manga')
            ->leftJoin('manga.likes', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->groupBy('manga.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Manga[]
     */
    public function findMostPopularByCategory(string $category, int $limit): array
    {
        return $this->createQueryBuilder('manga')
            ->leftJoin('manga.likes', 'likes')
            ->addSelect('COUNT(likes.id) as HIDDEN likeCount')
            ->where('manga.categories LIKE :category')
            ->setParameter('category', '%'.$category.'%')
            ->groupBy('manga.id')
            ->orderBy('likeCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Manga[]
     */
    public function findByDateAndByCategory(string $category, int $limit): array
    {
        return $this->createQueryBuilder('manga')
            ->where('manga.categories LIKE :category')
            ->setParameter('category', '%'.$category.'%')
            ->orderBy('manga.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, Manga>
     */
    public function search(string $query, int $maxResult): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.title LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }
}
