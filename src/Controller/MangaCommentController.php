<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Manga;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MangaCommentController extends AbstractController
{
    #[Route('/manga/{id}/comment', name: 'app_manga_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Manga $manga, EntityManagerInterface $em, Request $request, LoggerInterface $logger): Response
    {
        $content = $request->request->get('comment-content');

        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        $mangaId = $manga->getId();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (null !== $content && '' !== trim((string) $content)) {
            try {
                $comment = new Comment();
                $comment->setManga($manga);
                $comment->setTextContent((string) $content);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setUser($user);
                $commentId = $comment->getId();

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

                $logger->info(\sprintf(
                    'AJOUT COMMENTAIRE MANGA : Le commentaire #%d de l\'utilisateur #%s a été ajouté avec succès au manga #%s',
                    $commentId,
                    $pseudo,
                    $mangaId,
                ));
            } catch (\Exception $e) {
                $logger->error(\sprintf(
                    'ERREUR AJOUT COMMENTAIRE MANGA : L\'ajout du commentaire de l\'utilisateur #%s a échoué au manga #%s',
                    $pseudo,
                    $mangaId,
                ), ['exception' => $e]);
            }
        }

        return $this->redirectToRoute('app_info_manga', [
            'id' => $manga->getId(),
        ]);
    }
}
