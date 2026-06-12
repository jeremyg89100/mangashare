<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function unreadNotificationNumber(?User $user): int
    {
        if (!$user instanceof User) {
            return 0;
        }

        return (int) $this->createQueryBuilder('notification')
            ->select('count(notification.id)')
            ->where('notification.user = :user')
            ->andWhere('notification.hasBeenRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
