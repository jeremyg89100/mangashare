<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReadArticleController extends AbstractController
{
    #[Route('/read/article/{id}', name: 'app_read_article')]
    public function index(Article $article): Response
    {
        return $this->render('read_article/index.html.twig', [
            'article' => $article,
        ]);
    }
}
