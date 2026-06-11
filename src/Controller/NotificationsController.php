<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class NotificationsController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    #[IsGranted('ROLE_USER')]
    public function index(NotificationRepository $notificationRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $notifications = $notificationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        $unreadCount = $notificationRepository->unreadNotificationNumber($user);

        if ($unreadCount > 0) {
            foreach ($notifications as $notification) {
                if ($notification->hasBeenRead()) {
                    $notification->setHasBeenRead(true);
                }
            }
            $unreadCount = 0;

            $em->flush();
        }

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/notifications/mark-read', name: 'app_notifications_mark_read', methods: ['POST'])]
    public function markAllRead(NotificationRepository $notificationRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy([
            'user' => $user,
            'hasBeenRead' => false,
        ]);

        foreach ($notifications as $notification) {
            $notification->setHasBeenRead(true);
        }

        $em->flush();

        return $this->json(['success' => true]);
    }
}
