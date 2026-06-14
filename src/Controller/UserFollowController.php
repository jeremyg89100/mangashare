<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserFollowController extends AbstractController
{
    #[Route('/user/{id}/follow', name: 'app_user_follow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(User $targetUser, EntityManagerInterface $em, Request $request, LoggerInterface $logger): JsonResponse
    {
        if (!$this->isCsrfTokenValid('follow', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }

        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour suivre un utilisateur.'], 403);
        }
        $pseudoCurrentUser = $currentUser->getPseudo();
        $pseudoTargetUser = $targetUser->getPseudo();

        if ($currentUser === $targetUser) {
            return $this->json(['error' => 'Vous ne pouvez pas vous suivre vous-même'], 403);
        }

        $followRepository = $em->getRepository(Follow::class);
        $existingFollow = $followRepository->findOneBy([
            'follower' => $currentUser,
            'following' => $targetUser,
        ]);

        $isFollowing = false;

        try {
            if ($existingFollow instanceof Follow) {
                $em->remove($existingFollow);
                $logger->info(\sprintf(
                    'UNFOLLOW D\'UTILISATEUR : L\'utilisateur %s ne suit plus l\'utilisateur %s',
                    $pseudoCurrentUser,
                    $pseudoTargetUser
                ));
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
                $notification->setMessage("{$pseudoCurrentUser} vous suit");

                $em->persist($notification);

                $logger->info(\sprintf(
                    'FOLLOW D\'UTILISATEUR : L\'utilisateur %s a follow  l\'utilisateur %s avec succès',
                    $pseudoCurrentUser,
                    $pseudoTargetUser,
                ));
            }
            $em->flush();

            return $this->json([
                'success' => true,
                'isFollowing' => $isFollowing,
                'followersCount' => \count($targetUser->getFollowsAsFollowing()),
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR DE FOLLOW D\'UTILISATEUR : L\'utilisateur %s a échoué à follow  l\'utilisateur %s',
                $pseudoCurrentUser,
                $pseudoTargetUser,
            ), ['exception' => $e]);

            return $this->json([
                'success' => false,
                'error' => 'Une erreur interne est survenue.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
