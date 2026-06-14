<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class CommentSignalerController extends AbstractController
{
    #[Route('/comment/{id}/signaler', name: 'app_comment_signaler', methods: ['POST'])]
    public function index(Comment $comment, EntityManagerInterface $em, UserRepository $userRepository): JsonResponse
    {
        $comment->setIsReported(true);
        $em->flush();

        /** @var User[] $admins */
        $admins = $userRepository->findByRole('ROLE_ADMIN');

        foreach ($admins as $admin) {
            $notification = new Notification();
            $notification->setCreatedAt(new \DateTimeImmutable());
            $notification->setUser($admin);
            $notification->setHasBeenRead(false);
            $notification->setLink($this->generateUrl('app_admin'));

            if (null !== $comment->getManga()) {
                $notification->setMessage('Nouveau signalement sur le manga : '.$comment->getManga()->getTitle());
            } else {
                $article = $comment->getArticle();
                if (null !== $article) {
                    $notification->setMessage("Nouveau signalement sur l'article : ".$article->getTitle());
                } else {
                    $notification->setMessage('Nouveau signalement sur un contenu inconnu');
                }
            }

            $em->persist($notification);
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Le commentaire #'.$comment->getId().'a été signalé.',
        ]);
    }
}
