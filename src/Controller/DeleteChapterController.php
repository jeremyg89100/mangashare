<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteChapterController extends AbstractController
{
    #[Route('/delete/chapter/{id}', name: 'app_delete_chapter')]
    #[IsGranted('ROLE_USER')]
    public function index(Chapter $chapter, EntityManagerInterface $em, LoggerInterface $logger, #[Autowire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {
        $mangaId = $chapter->getManga()->getId();
        $chapterId = $chapter->getId();

        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        if ($chapter->getManga()->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer le chapitre d\'un autre utilisateur.');
        }

        $pageFiles = [];
        foreach ($chapter->getPages() as $page) {
            $pageFiles[] = $page->getImageUrl();
        }

        try {
            $em->remove($chapter);
            $em->flush();

            foreach ($pageFiles as $fileName) {
                $path = $pageDirectory.'/'.$fileName;
                if (is_file($path)) {
                    unlink($path);
                }
            }

            $logger->info(\sprintf(
                'SUPPRESSION CHAPITRE : Le chapitre #%d du manga #%d a été supprimé avec succès par l\'utilisateur %s',
                $chapterId,
                $mangaId,
                $pseudo
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'ERREUR SUPPRESSION CHAPITRE : Le chapitre #%d du manga #%d a échoué à être supprimé par l\'utilisateur %s',
                $chapterId,
                $mangaId,
                $pseudo
            ), ['exception' => $e]);
        }

        return $this->redirectToRoute('app_edit_manga', ['id' => $mangaId]);
    }
}
