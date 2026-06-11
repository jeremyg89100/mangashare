<?php

namespace App\Controller;

use App\Entity\Manga;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteMangaController extends AbstractController
{
    #[Route('/delete/manga/{id}', name: 'app_delete_manga')]
    #[IsGranted('ROLE_USER')]
    public function index(Manga $manga, EntityManagerInterface $em): Response
    {
        $em->remove($manga);
        $em->flush();

        return $this->redirectToRoute('app_projects');
    }
}
