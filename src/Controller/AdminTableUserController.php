<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminTableUserController extends AbstractController
{
    #[Route('/admin/table/user', name: 'app_admin_table_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByRole('ROLE_USER');

        return $this->render('admin_table_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/ban', name: 'app_admin_user_ban')]
    #[IsGranted('ROLE_ADMIN')]
    public function banUser(User $targetUser, Request $request, LoggerInterface $logger, EntityManagerInterface $em): JsonResponse
    {
        $isEnabled = $targetUser->isEnabled();
        $admin = $this->getUser();

        $adminPseudo = ($admin instanceof User) ? $admin->getPseudo() : 'Admin Inconnu';

        $userPseudo = $targetUser->getPseudo();

        $accountState = '';

        if (!$this->isCsrfTokenValid('ban', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Erreur : Token invalide'], Response::HTTP_FORBIDDEN);
        }

        try {
            if (true === $isEnabled) {
                $targetUser->setEnabled(false);
            } else {
                $targetUser->setEnabled(true);
            }

            $em->flush();

            $newStatusEnabled = $targetUser->isEnabled();

            if ($newStatusEnabled) {
                $accountState = 'débanni';
            } else {
                $accountState = 'banni';
            }

            $logger->info(\sprintf(
                'INFO BAN : L\'utilisateur %s n\'a été %s par l\'administrateur %s',
                $userPseudo,
                $accountState,
                $adminPseudo,
            ));

            return $this->json([
                'success' => true,
                'isEnabled' => $newStatusEnabled,
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR BAN : L\'utilisateur %s n\'a pas pu être %s par l\'administrateur %s',
                $userPseudo,
                $accountState,
                $adminPseudo,
            ), ['exception' => $e]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Une erreur lors du changement d\'état du ban est survenue',
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $targetUser, Request $request, LoggerInterface $logger, EntityManagerInterface $em): JsonResponse
    {
        $admin = $this->getUser();

        $adminPseudo = ($admin instanceof User) ? $admin->getPseudo() : 'Admin Inconnu';

        $userPseudo = $targetUser->getPseudo();

        if (!$this->isCsrfTokenValid('delete', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Erreur : Token invalide'], Response::HTTP_FORBIDDEN);
        }

        try {
            $em->remove($targetUser);
            $em->flush();

            $logger->info(\sprintf(
                'INFO SUPPRESSION COMPTE : L\'utilisateur %s a été supprimé par l\'administrateur %s',
                $userPseudo,
                $adminPseudo,
            ));

            return $this->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR SUPPRESSION COMPTE : L\'utilisateur %s n\'a pas pu être supprimé par l\'administrateur %s',
                $userPseudo,
                $adminPseudo,
            ), ['exception' => $e]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Une erreur lors de la suppression de l\'utilisateur est survenue',
        ]);
    }
}
