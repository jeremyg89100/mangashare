<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteCommentController extends AbstractController
{
    #[Route('/admin/delete/comment/{id}', name: 'app_delete_comment')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Comment $comment, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        $commentId = $comment->getId();
        $commentUser = $comment->getUser();
        $author = $commentUser->getPseudo();

        $adminUser = $this->getUser();
        $adminIdentifier = (null !== $adminUser) ? $adminUser->getUserIdentifier() : 'Admin Inconnu';

        try {
            $em->remove($comment);
            $em->flush();

            $logger->info(\sprintf(
                '##ACTION ADMIN ## Le commentaire #%d (Auteur : %s) a été supprimé par l\'administrateur %s',
                $commentId,
                $author,
                $adminIdentifier,
            ));

            return new JsonResponse([
                'success' => true,
                'message' => 'Le commentaire #'.$commentId.' a bien été supprimé.',
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                '##ACTION ADMIN ## Erreur lors de la suppression du commentaire #%d (Auteur : %s) par l\'administrateur %s.',
                $commentId,
                $adminIdentifier,
                $e->getMessage(),
            ), ['exception' => $e]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Le commentaire #'.$commentId.' a bien été supprimé.',
            ]);
        }
    }
}
