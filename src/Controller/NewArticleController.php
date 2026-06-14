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

final class NewArticleController extends AbstractController
{
    #[Route('/new/article', name: 'app_new_article')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, SluggerInterface $slugger, LoggerInterface $logger, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatureArticle')] string $miniatureDirectory): Response
    {
        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        if ($request->isMethod('POST')) {
            try {
                $articleTitle = $request->request->getString('articleTitle');
                $articleContent = $request->request->getString('article-content');
                \assert($user instanceof User);
                $miniatureFile = $request->files->get('miniature');
                $categories = $request->request->getString('category');
                $published = $request->request->getBoolean('published');

                $article = new Article();
                $article->setUser($user);
                $article->setTitle($articleTitle);
                $article->setCategory($categories);
                $article->setPublished($published);
                $article->setCreatedAt(new \DateTimeImmutable());
                $article->setTextContent($articleContent);
                $articleId = $article->getId();

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
                    'AJOUT NOUVEL ARTICLE : L\'article #%d de l\'utilisateur #%s a été ajouté avec succès',
                    $articleId,
                    $pseudo,
                ));

                return $this->redirectToRoute('app_my_articles');
            } catch (\Exception $e) {
                $logger->error(\sprintf(
                    'ERREUR AJOUT NOUVEL ARTICLE : L\'ajout de l\'article de l\'utilisateur #%s a échoué',
                    $pseudo,
                ), ['exception' => $e]);
            }
        }

        return $this->render('new_article/index.html.twig', []);
    }
}
