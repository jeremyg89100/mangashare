<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\LikeArticle;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArticleLikeController extends AbstractController
{
    #[Route('/article/{id}/like', name: 'app_article_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Article $article, EntityManagerInterface $em, Request $request, LoggerInterface $logger): JsonResponse
    {
        if (!$this->isCsrfTokenValid('like', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer cet article.'], 403);
        }

        $pseudo = $user->getPseudo();
        $articleId = $article->getId();

        $liked = false;

        try {
            $likeRepository = $em->getRepository(LikeArticle::class);
            $existingLike = $likeRepository->findOneBy([
                'user' => $user,
                'article' => $article,
            ]);

            if ($existingLike instanceof LikeArticle) {
                $article->removeLikeArticle($existingLike);
                $em->remove($existingLike);

                $logger->info(\sprintf("RETRAIT LIKE : L'utilisateur %s n'aime plus l'article #%d", $pseudo, $articleId));
            } else {
                $like = new LikeArticle();
                $like->setUser($user);
                $like->setArticle($article);
                $article->addLikeArticle($like);
                $em->persist($like);
                $liked = true;

                $url = $this->generateUrl('app_read_article', [
                    'id' => $article->getId(),
                ]);

                $notification = new Notification();
                $notification->setUser($article->getUser());
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->setMessage("\"{$article->getTitle()}\" a été aimé");
                $notification->setLink($url);
                $notification->setHasBeenRead((bool) 0);

                $em->persist($notification);
            }

            $em->flush();
            $logger->info(\sprintf(
                'AJOUT LIKE : L\'utilisateur %s a aimé l\'article #%d ',
                $pseudo,
                $articleId
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR AJOUT LIKE : L\'utilisateur %s a échoué à aimer l\'article #%d ',
                $pseudo,
                $articleId
            ), ['exception' => $e]);
        }

        return $this->json([
            'success' => true,
            'newLikeCount' => \count($article->getLikeArticles()),
            'isLiked' => $liked,
        ]);
    }
}
