<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Manga;
use App\Entity\Chapter;
use App\Repository\ChapterRepository;
use App\Repository\MangaRepository;

final class ReadMangaController extends AbstractController
{
    #[Route('/read/manga/{mangaId}/{chapterId}', name: 'app_read_manga')]
    public function index(int $mangaId, int $chapterId, MangaRepository $mangaRepository, ChapterRepository $chapterRepository): Response
    {
        $manga = $mangaRepository->find($mangaId);
        $chapter = $chapterRepository->find($chapterId);

        if ($manga instanceof Manga || $chapter instanceof Chapter) {
            throw $this->createNotFoundException();
        }

        return $this->render('read_manga/index.html.twig', [
            'manga' => $manga,
            'chapter' => $chapter,
        ]);
    }
}
