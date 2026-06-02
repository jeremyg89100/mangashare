<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Follow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

final class UserFollowController extends AbstractController
{
    #[Route('/user/{id}/follow', name: 'app_user_follow', methods:['POST'])]
    public function index(User $targetUser, EntityManagerInterface $em, Request $request): JsonResponse
    {   
        if (!$this->isCsrfTokenValid('follow', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }

        $currentUser = $this->getUser();

        if ($currentUser === null || $currentUser instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer ce manga.'], 403);
        }

         if ($currentUser === $targetUser) {
            return $this->json(['error' => 'Vous ne pouvez pas vous suivre vous-même'], 403);
        }

        $followRepository = $em->getRepository(\App\Entity\Follow::class);
        $existingFollow = $followRepository->findOneBy([
            'follower' => $currentUser,
            'following' => $targetUser,
        ]);

        if ($existingFollow instanceof Follow) {
            $em->remove($existingFollow);
            $isFollowing = false;
        }

        else {
            $follow = new Follow;
            $follow->setFollower($currentUser);
            $follow->setFollowing($targetUser);
            $follow->setCreatedAt(new \DateTimeImmutable());
            $em->persist($follow);
            $isFollowing = true;
        }
        $em->flush();

        return $this->json([
            'success' => true,
            'isFollowing' => $isFollowing,
            'followersCount' => count($targetUser->getFollowsAsFollowing()),
        ]);
    }
}
