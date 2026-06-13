<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(CommentRepository $commentRepository): Response
    {
        $reported = $commentRepository->findBy(['isReported' => true]);

        return $this->render('admin/index.html.twig', [
            'comments' => $reported,
        ]);
    }
}
