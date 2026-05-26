<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Manga;

final class InfoMangaController extends AbstractController
{
    #[Route('/info/manga/{id}', name: 'app_info_manga')]
    public function index(Manga $manga): Response
    {   
        return $this->render('info_manga/index.html.twig', [
            'manga' => $manga,
        ]);
    }
}
