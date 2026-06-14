<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteReportController extends AbstractController
{
    #[Route('/admin/delete/report/comment/{id}', name: 'app_delete_report', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Comment $comment): JsonResponse
    {
        $commentId = $comment->getId();

        return new JsonResponse([
            'success' => true,
            'message' => 'Le signalement de #'.$commentId.' a bien été supprimé.',
        ]);
    }
}
