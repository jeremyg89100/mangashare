<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\User;
use App\Entity\View;
use App\Repository\ChapterRepository;
use App\Repository\MangaRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReadMangaController extends AbstractController
{
    #[Route('/read/manga/{mangaId}/{chapterId}', name: 'app_read_manga')]
    public function index(int $mangaId, int $chapterId, MangaRepository $mangaRepository, ChapterRepository $chapterRepository, ViewRepository $viewRepository, EntityManagerInterface $em): Response
    {
        $manga = $mangaRepository->find($mangaId);
        $chapter = $chapterRepository->find($chapterId);
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$manga instanceof Manga || !$chapter instanceof Chapter) {
            throw $this->createNotFoundException();
        }

        // On n'enregistre qu'une seule vue par utilisateur et par manga sur une meme journee,
        // sinon recharger la page gonflerait artificiellement les statistiques.
        $now = new \DateTimeImmutable();
        if (!$viewRepository->hasViewedMangaOnDay($user, $manga, $now)) {
            $view = new View();
            $view->setManga($manga);
            $view->setUser($user);
            $view->setCreatedAt($now);
            $em->persist($view);
            $em->flush();
        }

        return $this->render('read_manga/index.html.twig', [
            'manga' => $manga,
            'chapter' => $chapter,
        ]);
    }
}
