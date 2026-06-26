<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PopularController extends AbstractController
{
    #[Route('/popular', name: 'app_popular')]
    public function index(MangaRepository $mangaRepository, Request $request): Response
    {
        $category = $request->query->getString('category');
        $categories = [
            'Action',
            'Romance',
            'Fantastique',
            'Tranche de vie',
            'Horreur',
            'Comédie',
            'Sport',
        ];

        if ('' !== $category && \in_array($category, $categories, true)) {
            $popular = $mangaRepository->findMostPopularByCategory($category, 12);
        } else {
            $popular = $mangaRepository->findMostPopular(12);
            $category = null;
        }

        return $this->render('popular/index.html.twig', [
            'popular' => $popular,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }
}
