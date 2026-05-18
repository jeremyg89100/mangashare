<?php

namespace App\Controller;

use App\Controller\SecurityController;
use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectsController extends AbstractController
{
    #[Route('/projects', name: 'app_projects')]
    public function index(SecurityController $securityController, MangaRepository $mangaRepository): Response
    {
        $user = $securityController->getUser();
        $manga = $mangaRepository->findBy([], ['createdAt' => 'DESC'], '6');
        return $this->render('projects/index.html.twig', [
            'user' => $user,
            'manga' => $manga,
            'controller_name' => 'ProjectsController',
        ]);
    }
}
