<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EditArticlesController extends AbstractController
{
    #[Route('/edit/articles/{id}', name: 'app_edit_articles')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Article $article,
        Request $request,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/miniatureArticle')]
        string $miniatureDirectory
    ): Response {
        $articleId = $article->getId();
        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        if ($request->isMethod('POST')) {
            try {
                $article->setTitle($request->request->getString('articleTitle'));
                $article->setCategory($request->request->getString('category'));
                $article->setPublished($request->request->getBoolean('published'));
                $article->setTextContent($request->request->getString('article-content'));

                $miniatureFile = $request->files->get('miniature');

                if ($miniatureFile instanceof UploadedFile) {
                    $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), \PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($originalFileName);
                    $newFileName = $safeFileName.'-'.uniqid().'.'.$miniatureFile->guessExtension();

                    $miniatureFile->move($miniatureDirectory, $newFileName);

                    $article->setMiniature($newFileName);
                }

                $em->persist($article);
                $em->flush();

                $logger->info(\sprintf(
                    'EDIT ARTICLE : L\'article #%d a été modifié avec succès par l\'utilisateur %s',
                    $articleId,
                    $pseudo
                ));

                return $this->redirectToRoute('app_my_articles');
            } catch (\Exception $e) {
                $logger->error(\sprintf(
                    'EDIT ARTICLE ERREUR : La supression de l\'article #%d a échoué par l\'utilisateur %s',
                    $articleId,
                    $pseudo
                ), ['exception' => $e]);
            }
        }

        return $this->render('edit_articles/index.html.twig', [
            'article' => $article,
        ]);
    }
}
