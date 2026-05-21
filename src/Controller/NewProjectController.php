<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewProjectController extends AbstractController
{
    #[Route('/new/project', name: 'app_new_project')]
    public function index(): Response
    {
        return $this->render('new_project/index.html.twig', [
            'controller_name' => 'NewProjectController',
        ]);
    }
}
