<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditChapterController extends AbstractController
{
    #[Route('/edit/chapter', name: 'app_edit_chapter')]
    public function index(): Response
    {
        return $this->render('edit_chapter/index.html.twig', [
            'controller_name' => 'EditChapterController',
        ]);
    }
}
