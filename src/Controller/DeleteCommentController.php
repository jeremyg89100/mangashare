<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteCommentController extends AbstractController
{
    #[Route('/admin/delete/comment/{id}', name: 'app_delete_comment')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Comment $comment, EntityManagerInterface $em): JsonResponse
    {
        $commentId = $comment->getId();

        $em->remove($comment);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Le commentaire #'.$commentId.' a bien été supprimé.',
        ]);
    }
}
