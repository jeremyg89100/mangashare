<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArticleCommentController extends AbstractController
{
    #[Route('/article/{id}/comment', name: 'app_article_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Article $article, EntityManagerInterface $em, Request $request): Response
    {
        $content = $request->request->get('comment-content');
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (null !== $content && '' !== trim((string) $content)) {
            $comment = new Comment();
            $comment->setArticle($article);
            $comment->setTextContent((string) $content);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($user);

            $em->persist($comment);

            $url = $this->generateUrl('app_read_article', [
                'id' => $article->getId(),
            ]);

            $notification = new Notification();
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setUser($user);
            $notification->setHasBeenRead((bool) 0);
            $notification->setLink($url);
            $notification->setMessage("\"{$article->getTitle()}\" a été commenté");

            $em->persist($notification);

            $em->flush();
        }

        return $this->redirectToRoute('app_read_article', [
            'id' => $article->getId(),
        ]);
    }
}
