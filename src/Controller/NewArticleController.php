<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class NewArticleController extends AbstractController
{
    #[Route('/new/article', name: 'app_new_article')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatureArticle')] string $miniatureDirectory): Response
    {
        if ($request->isMethod('POST')) {
            $articleTitle = $request->request->getString('articleTitle');
            $articleContent = $request->request->getString('article-content');
            $user = $this->getUser();
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

            if ($miniatureFile instanceof UploadedFile) {
                $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), \PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$miniatureFile->guessExtension();

                $miniatureFile->move($miniatureDirectory, $newFileName);

                $article->setMiniature($newFileName);
            }

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_my_articles');
        }

        return $this->render('new_article/index.html.twig', []);
    }
}
