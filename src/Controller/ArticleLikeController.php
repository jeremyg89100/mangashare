<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\LikeArticle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleLikeController extends AbstractController
{
    #[Route('/article/{id}/like', name: 'app_article_like')]
    public function index(Article $article, EntityManagerInterface $em, Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('like', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }
        $user = $this->getUser();

        if (null === $user || !$user instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer ce manga.'], 403);
        }

        $likeRepository = $em->getRepository(LikeArticle::class);
        $existingLike = $likeRepository->findOneBy([
            'user' => $user,
            'article' => $article,
        ]);

        if ($existingLike instanceof LikeArticle) {
            $article->removeLikeArticle($existingLike);
            $em->remove($existingLike);
            $liked = false;
        } else {
            $like = new LikeArticle();
            $like->setUser($user);
            $like->setArticle($article);
            $article->addLikeArticle($like);
            $em->persist($like);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'newLikeCount' => \count($article->getLikeArticles()),
            'isLiked' => $liked,
        ]);
    }
}
