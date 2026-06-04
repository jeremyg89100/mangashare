<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleCommentController extends AbstractController
{
    #[Route('/article/{id}/comment', name: 'app_article_comment')]
    public function index(Article $article, EntityManagerInterface $em, Request $request): Response
    {
        $content = $request->request->get('comment-content');
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour commenter.');
        }

        if (null !== $content && '' !== trim((string) $content)) {
            $comment = new Comment();
            $comment->setArticle($article);
            $comment->setTextContent((string) $content);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($user);

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_read_article', [
            'id' => $article->getId(),
        ]);
    }
}
