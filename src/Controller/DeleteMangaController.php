<?php

namespace App\Controller;

use App\Entity\Manga;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteMangaController extends AbstractController
{
    #[Route('/delete/manga/{id}', name: 'app_delete_manga')]
    public function index(Manga $manga, EntityManagerInterface $em): Response
    {
        $em->remove($manga);
        $em->flush();

        return $this->redirectToRoute('app_projects');

        return $this->render('delete_manga/index.html.twig', [
            'controller_name' => 'DeleteMangaController',
        ]);
    }
}
