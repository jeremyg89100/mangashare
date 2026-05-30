<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyArticlesController extends AbstractController
{
    #[Route('/my/articles', name: 'app_my_articles')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $category = $request->query->get('category');
        $categories = [
            'Dessin & Anatomies',
            'Scenario & Storytelling',
            'Techniques & Logiciels',
            'Edition & Publication',
        ];

        if ($category && \in_array($category, $categories)) {
            $articles = $articleRepository->findMostPopularByCategory($category, 12);
        } else {
            $articles = $articleRepository->findMostPopular(12);
            $category = null;
        }

        return $this->render('my_articles/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }
}
