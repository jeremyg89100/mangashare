<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Manga;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MangaLikeController extends AbstractController
{
    #[Route('/manga/{id}/like', name: 'app_manga_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Manga $manga, EntityManagerInterface $em, Request $request, LoggerInterface $logger): JsonResponse
    {
        if (!$this->isCsrfTokenValid('like', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token invalide'], 403);
        }
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => "L'utilisateur n'est pas connecté"], 404);
        }

        $pseudo = $user->getPseudo();
        $mangaId = $manga->getId();

        $liked = false;

        try {
            $likeRepository = $em->getRepository(Like::class);
            $existingLike = $likeRepository->findOneBy([
                'user' => $user,
                'manga' => $manga,
            ]);

            if ($existingLike instanceof Like) {
                $manga->removeLike($existingLike);
                $em->remove($existingLike);
            } else {
                $like = new Like();
                $like->setUser($user);
                $like->setManga($manga);
                $like->setCreatedAt(new \DateTimeImmutable());
                $manga->addLike($like);
                $em->persist($like);
                $liked = true;

                $url = $this->generateUrl('app_info_manga', [
                    'id' => $manga->getId(),
                ]);

                $notification = new Notification();
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->setUser($user);
                $notification->setHasBeenRead((bool) 0);
                $notification->setLink($url);
                $notification->setMessage("\"{$manga->getTitle()}\" a été aimé");

                $em->persist($notification);
            }

            $em->flush();
            $logger->info(\sprintf(
                'AJOUT LIKE MANGA : Le like de l\'utilisateur #%s a été ajouté avec succès au manga #%s',
                $pseudo,
                $mangaId,
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR AJOUT LIKE MANGA : L\'ajout du like de l\'utilisateur #%s a échoué au manga #%s',
                $pseudo,
                $mangaId,
            ), ['exception' => $e]);
        }

        return $this->json([
            'success' => true,
            'newLikeCount' => \count($manga->getLikes()),
            'isLiked' => $liked,
        ]);
    }
}
