<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewContentController extends AbstractController
{
    #[Route('/new/content', name: 'app_new_content')]
    public function index(MangaRepository $mangaRepository): Response
    {
        $newContent = $mangaRepository->findBy([], ['createdAt' => 'DESC'], '6');

        return $this->render('new_content/index.html.twig', [
            'newContent' => $newContent,
            'controller_name' => 'NewContentController',
        ]);
    }
}
