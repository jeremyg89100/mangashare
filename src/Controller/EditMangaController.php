<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\EditMangaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EditMangaController extends AbstractController
{
    #[Route('/edit/manga/{id}', name: 'app_edit_manga')]
    #[IsGranted('ROLE_USER')]
    public function index(Manga $manga): Response
    {
        if ($manga->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(EditMangaType::class, $manga);

        return $this->render('edit_manga/index.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }
}
