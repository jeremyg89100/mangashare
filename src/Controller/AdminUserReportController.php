<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserReportController extends AbstractController
{
    #[Route('/admin/user/report', name: 'app_admin_user_report')]
    public function index(UserRepository $userRepository): Response
    {
        $reportedUsers = $userRepository->findBy(['isReported' => true]);

        return $this->render('admin_user_report/index.html.twig', [
            'reportedUsers' => $reportedUsers,
        ]);
    }
}
