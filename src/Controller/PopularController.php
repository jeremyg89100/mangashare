<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PopularController extends AbstractController
{
    #[Route('/popular', name: 'app_popular')]
    public function index(MangaRepository $mangaRepository): Response
    {   
        $popular = $mangaRepository->findMostPopular(6);
        return $this->render('popular/index.html.twig', [
            'popular' => $popular,
        ]);
    }
}
