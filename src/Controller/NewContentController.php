<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewContentController extends AbstractController
{
    #[Route('/new/content', name: 'app_new_content')]
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
            $newContent = $mangaRepository->findByDateAndByCategory($category, 12);
        } else {
            $newContent = $mangaRepository->findBy([], ['createdAt' => 'DESC'], 12);
            $category = null;
        }

        return $this->render('new_content/index.html.twig', [
            'newContent' => $newContent,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }
}
