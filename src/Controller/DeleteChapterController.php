<?php

namespace App\Controller;

use App\Entity\Chapter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteChapterController extends AbstractController
{
    #[Route('/delete/chapter/{id}', name: 'app_delete_chapter')]
    #[IsGranted('ROLE_USER')]
    public function index(Chapter $chapter, Request $request, EntityManagerInterface $em): Response
    {
        $mangaId = $chapter->getManga()->getId();

        $em->remove($chapter);
        $em->flush();

        return $this->redirectToRoute('app_edit_manga', ['id' => $mangaId]);
    }
}
