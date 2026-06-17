<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteReportUserController extends AbstractController
{
    #[Route('/admin/delete/report/user/{id}', name: 'app_delete_report_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(User $user, EntityManagerInterface $em, LoggerInterface $logger): JsonResponse
    {
        $userPseudo = $user->getPseudo();
        $admin = $this->getUser();
        $adminPseudo = ($admin instanceof User) ? $admin->getPseudo() : 'Administrateur inconnu';

        try {
            $user->setIsReported(false);
            $user->setReportReason(null);
            $em->flush();
            $logger->info(\sprintf(
                'SIGNALEMENT RETIRE :  Le signalement de l\'utilisateur #%s a été retiré par l\'administrateur %s ',
                $userPseudo,
                $adminPseudo,
            ));

            return new JsonResponse([
                'success' => true,
                'message' => 'Le signalement de '.$userPseudo.' a bien été supprimé.',
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR SIGNALEMENT RETIRE :  Le signalement du commentaire #%s n\'a pas pu être retiré par l\'administrateur %s ',
                $userPseudo,
                $adminPseudo,
            ), ['exception' => $e]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Le signalement de '.$userPseudo.' a échoué.',
            ]);
        }
    }
}
