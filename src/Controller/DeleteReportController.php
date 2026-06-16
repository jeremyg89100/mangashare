<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteReportController extends AbstractController
{
    #[Route('/admin/delete/report/{id}', name: 'app_delete_report', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Comment $comment, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        $commentId = $comment->getId();
        $admin = $this->getUser();
        $adminPseudo = ($admin instanceof User) ? $admin->getPseudo() : 'Administrateur inconnu';

        try {
            $comment->setIsReported(false);
            $em->flush();
            $logger->info(\sprintf(
                'SIGNALEMENT RETIRE :  Le signalement du commentaire #%d a été retiré par l\'administrateur %s ',
                $commentId,
                $adminPseudo,
            ));

            return new JsonResponse([
                'success' => true,
                'message' => 'Le signalement de #'.$commentId.' a bien été supprimé.',
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR SIGNALEMENT RETIRE :  Le signalement du commentaire #%d n\'a pas pu être retiré par l\'administrateur %s ',
                $commentId,
                $adminPseudo,
            ), ['exception' => $e]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Le signalement de #'.$commentId.' a échoué.',
            ]);
        }
    }
}
