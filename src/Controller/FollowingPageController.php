<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FollowingPageController extends AbstractController
{
    #[Route('/following/page', name: 'app_following_page')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $follows = $user->getFollowsAsFollower();

        return $this->render('following_page/index.html.twig', [
            'follows' => $follows,
        ]);
    }
}
