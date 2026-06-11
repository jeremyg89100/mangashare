<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserFollowController extends AbstractController
{
    #[Route('/user/{id}/follow', name: 'app_user_follow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(User $targetUser, EntityManagerInterface $em, Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('follow', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }

        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour suivre un utilisateur.'], 403);
        }

        if ($currentUser === $targetUser) {
            return $this->json(['error' => 'Vous ne pouvez pas vous suivre vous-même'], 403);
        }

        $followRepository = $em->getRepository(Follow::class);
        $existingFollow = $followRepository->findOneBy([
            'follower' => $currentUser,
            'following' => $targetUser,
        ]);

        if ($existingFollow instanceof Follow) {
            $em->remove($existingFollow);
            $isFollowing = false;
        } else {
            $follow = new Follow();
            $follow->setFollower($currentUser);
            $follow->setFollowing($targetUser);
            $follow->setCreatedAt(new \DateTimeImmutable());
            $em->persist($follow);
            $isFollowing = true;

            $notification = new Notification();
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setHasBeenRead(false);
            $notification->setUser($targetUser);
            $notification->setMessage("{$currentUser->getPseudo()} vous suit");

            $em->persist($notification);
        }
        $em->flush();

        return $this->json([
            'success' => true,
            'isFollowing' => $isFollowing,
            'followersCount' => \count($targetUser->getFollowsAsFollowing()),
        ]);
    }
}
