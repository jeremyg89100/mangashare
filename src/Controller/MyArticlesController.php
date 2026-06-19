<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MyArticlesController extends AbstractController
{
    #[Route('/my/articles', name: 'app_my_articles')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $category = $request->query->getString('category');
        $categories = [
            'Dessin & Anatomies',
            'Scenario & Storytelling',
            'Techniques & Logiciels',
            'Edition & Publication',
        ];

        if ('' !== $category && \in_array($category, $categories, true)) {
            $articles = $articleRepository->findByUserAndCategory($user, $category, 12);
        } else {
            $articles = $articleRepository->findByUser($user, 12);
            $category = null;
        }

        return $this->render('my_articles/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }
}
