<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserPageController extends AbstractController
{
    #[Route('/user/page', name: 'app_user_page')]
    public function index(SecurityController $securityController): Response
    {   
        $user = $securityController->getUser();
        return $this->render('user_page/index.html.twig', [
            'controller_name' => 'UserPageController',
        ]);
    }
}
