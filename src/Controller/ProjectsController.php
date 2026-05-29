<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProjectsController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    #[IsGranted('ROLE_USER')]
    public function index(MangaRepository $mangaRepository): Response
    {
        $user = $this->getUser();
        $manga = $mangaRepository->findBy([], ['createdAt' => 'DESC'], '6');

        return $this->render('projects/index.html.twig', [
            'user' => $user,
            'manga' => $manga,
        ]);
    }
}
