<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteMangaController extends AbstractController
{
    #[Route('/delete/manga/{id}', name: 'app_delete_manga', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Manga $manga,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Request $request,
        #[Autowire('%kernel.project_dir%/public/uploads/pages')]
        string $pageDirectory,
        #[Autowire('%kernel.project_dir%/public/uploads/miniatures')]
        string $miniatureDirectory,
    ): Response {
        $mangaId = $manga->getId();
        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        $token = (string) ($request->request->get('_token') ?? $request->headers->get('X-CSRF-Token'));
        if (!$this->isCsrfTokenValid('delete-'.$mangaId, $token)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Token CSRF invalide.'], 403);
            }
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        if ($manga->getUser() !== $user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer le manga d\'un autre utilisateur.');
        }

        $filesToDelete = [];

        if (null !== $manga->getMiniature()) {
            $filesToDelete[] = $miniatureDirectory.'/'.$manga->getMiniature();
        }

        foreach ($manga->getChapters() as $chapter) {
            foreach ($chapter->getPages() as $page) {
                $filesToDelete[] = $pageDirectory.'/'.$page->getImageUrl();
            }
        }

        try {
            $em->remove($manga);
            $em->flush();

            foreach ($filesToDelete as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }

            $logger->info(\sprintf(
                'SUPPRESSION MANGA : Le manga %d a été supprimé avec succès par l\'utilisateur %s',
                $mangaId,
                $pseudo
            ));
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'SUPPRESSION MANGA : La supression du manga #%d a échoué par l\'utilisateur %s',
                $mangaId,
                $pseudo
            ), ['exception' => $e]);
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false], 500);
            }
        }

        return $this->redirectToRoute('app_projects');
    }
}
