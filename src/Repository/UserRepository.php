<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return array<int, User>
     */
    public function search(string $query, int $maxResult): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.pseudo LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, User>
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('user')
            ->where('user.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%')
            ->getQuery()
            ->getResult();
    }
}
