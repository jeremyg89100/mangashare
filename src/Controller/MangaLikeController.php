<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Manga;
use App\Entity\Like;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

final class MangaLikeController extends AbstractController
{
    #[Route('/manga/{id}/like', name: 'app_manga_like', methods: ['POST'])]
    public function index(Manga $manga, EntityManagerInterface $em, Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('like', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }
        $user = $this->getUser();

        if ($user === null || !$user instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer ce manga.'], 403);
        }

        $likeRepository = $em->getRepository(\App\Entity\Like::class);
        $existingLike = $likeRepository->findOneBy([
            'user' => $user,
            'manga' => $manga,
        ]);

        
        if ($existingLike) {
            $manga->removeLike($existingLike);
            $em->remove($existingLike);
            $liked = false;
        } else {
            $like = new Like;
            $like->setUser($user);
            $like->setManga($manga);
            $like->setCreatedAt(new \DateTimeImmutable());
            $manga->addLike($like);
            $em->persist($like);
            $liked = true;
        }


        $em->flush();
        return $this->json([
            'success' => true,
            'newLikeCount' => count($manga->getLikes()),
            'isLiked' => $liked,
        ]);
    }
}
