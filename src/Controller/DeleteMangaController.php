<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteMangaController extends AbstractController
{
    #[Route('/delete/manga/{id}', name: 'app_delete_manga')]
    #[IsGranted('ROLE_USER')]
    public function index(Manga $manga, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        $mangaId = $manga->getId();
        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        try {
            $em->remove($manga);
            $em->flush();

            $logger->info(\sprintf(
                'SUPPRESSION MANGA : Le manga %d a été supprimé avec succès par l\'utilisateur %s',
                $mangaId,
                $pseudo
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'SUPPRESSION MANGA : La supression du manga #%d a échoué par l\'utilisateur %s',
                $mangaId,
                $pseudo
            ), ['exception' => $e]);
        }

        return $this->redirectToRoute('app_projects');
    }
}
