<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DeleteArticleController extends AbstractController
{
    #[Route('/delete/article/{id}', name: 'app_delete_article')]
    #[IsGranted('ROLE_USER')]
    public function index(Article $article, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        $articleId = $article->getId();
        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        if ($article->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer l\'article d\'un autre utilisateur.');
        }

        try {
            $em->remove($article);
            $em->flush();
            $logger->info(\sprintf(
                'SUPPRESSION ARTICLE : L\'article %d a été supprimé avec succès par l\'utilisateur %s',
                $articleId,
                $pseudo
            ));
        } catch (\Exception $e) {
            $logger->error(\sprintf(
                'SUPPRESSION ARTICLE : La supression de l\'article %d a échoué par l\'utilisateur %s',
                $articleId,
                $pseudo
            ), ['exception' => $e]);
        }

        return $this->redirectToRoute('app_my_articles');
    }
}
