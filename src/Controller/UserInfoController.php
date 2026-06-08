<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserInfoController extends AbstractController
{
    #[Route('/user/{id}/info', name: 'app_user_info')]
    public function index(User $user): Response
    {
        $mangas = $user->getMangas();
        $articles = $user->getArticles();

        return $this->render('user_info/index.html.twig', [
            'user' => $user,
            'mangas' => $mangas,
            'articles' => $articles,
        ]);
    }
}
