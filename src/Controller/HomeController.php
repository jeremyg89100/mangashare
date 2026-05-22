<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MangaRepository $mangaRepository): Response
    {
        $popular = $mangaRepository->findMostPopular(6);
        $newContent = $mangaRepository->findBy([], ['createdAt' => 'DESC'], 6);
        return $this->render('home/index.html.twig', [
            'popular' => $popular,
            'newContent' => $newContent,
        ]);
    }
}
