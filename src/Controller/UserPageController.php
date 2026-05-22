<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserPageController extends AbstractController
{
    #[Route('/user/page', name: 'app_user_page')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {   
        $user = $this->getUser();
        return $this->render('user_page/index.html.twig', [
            'user' => $user,
        ]);
    }
}
