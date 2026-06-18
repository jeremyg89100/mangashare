<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommentSignalerController extends AbstractController
{
    #[Route('/comment/{id}/signaler', name: 'app_comment_signaler', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Comment $comment, EntityManagerInterface $em, UserRepository $userRepository, LoggerInterface $logger): JsonResponse
    {
        $commentId = $comment->getId();
        try {
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
            $logger->info(\sprintf(
                'Le commentaire #%d a été signalé avec succès',
                $commentId
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'Le commentaire %d a échoué à être signalé',
                $commentId
            ), ['exception' => $e]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Le commentaire #'.$commentId.' a été signalé.',
        ]);
    }
}
