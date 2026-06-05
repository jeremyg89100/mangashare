<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Manga;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MangaCommentController extends AbstractController
{
    #[Route('/manga/{id}/comment', name: 'app_manga_comment', methods: ['POST'])]
    public function index(Manga $manga, EntityManagerInterface $em, Request $request): Response
    {
        $content = $request->request->get('comment-content');
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour commenter.');
        }

        if (null !== $content && '' !== trim((string) $content)) {
            $comment = new Comment();
            $comment->setManga($manga);
            $comment->setTextContent((string) $content);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($user);

            $url = $this->generateUrl('app_info_manga', [
                'id' => $manga->getId(),
            ]);

            $notification = new Notification();
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setUser($user);
            $notification->setHasBeenRead((bool) 0);
            $notification->setLink($url);
            $notification->setMessage("\"{$manga->getTitle()}\" a été commenté");

            $em->persist($notification);

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('app_info_manga', [
            'id' => $manga->getId(),
        ]);
    }
}
