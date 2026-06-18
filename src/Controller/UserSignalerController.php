<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserSignalerController extends AbstractController
{
    // Route hors du préfixe ^/admin : signaler est une action UTILISATEUR,
    // pas une action d'administration (sinon bloquée par l'access_control ROLE_ADMIN).
    #[Route('/user/{id}/signaler', name: 'app_user_signaler', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(LoggerInterface $logger, User $user, EntityManagerInterface $em, UserRepository $userRepository, Request $request): JsonResponse
    {
        $userPseudo = $user->getPseudo();
        $data = json_decode($request->getContent(), true);

        if (!\is_array($data)) {
            $data = [];
        }

        $reason = 'Aucune raison spécifiée';
        if (isset($data['reason']) && \is_string($data['reason'])) {
            $reason = $data['reason'];
        }

        try {
            $user->setIsReported(true);
            $user->setReportReason($reason);
            $em->flush();

            /** @var User[] $admins */
            $admins = $userRepository->findByRole('ROLE_ADMIN');

            foreach ($admins as $admin) {
                $notification = new Notification();
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->setUser($admin);
                $notification->setHasBeenRead(false);
                $notification->setLink($this->generateUrl('app_admin'));
                $notification->setMessage('Nouveau signalement sur l\'utilisateur : '.$userPseudo);

                $em->persist($notification);
            }

            $em->flush();
            $logger->info(\sprintf(
                'L\'utilisateur #%s a été signalé avec succès',
                $userPseudo
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'Le commentaire %d a échoué à être signalé',
                $userPseudo
            ), ['exception' => $e]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'L\'utilisateur #'.$userPseudo.'a été signalé.',
        ]);
    }
}
