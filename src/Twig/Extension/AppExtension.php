<?php

namespace App\Twig\Extension;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security
    ) {
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return [
                'unreadNotificationsCount' => 0,
                'notifications' => [],
            ];
        }

        return [
            'unreadNotificationsCount' => $this->notificationRepository->unreadNotificationNumber($user),
            'notifications' => $this->notificationRepository->findBy(
                ['user' => $user],
                ['createdAt' => 'DESC'],
                5
            ),
        ];
    }
}
