<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class NewArticleController extends AbstractController
{
    #[Route('/new/article', name: 'app_new_article')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, #[AutoWire('%kernel.project_dir%/public/uploads/miniatureArticle')] string $miniatureDirectory,): Response
    {
        if ($request->isMethod('POST')) {
            $articleTitle = $request->request->get('articleTitle');
            $articleContent = $request->request->get('article-content');
            $user = $this->getUser();
            $miniatureFile = $request->files->get('miniature');
            $categories = $request->request->get('category');
            $published = $request->request->get('published');

            $article = new Article();
            $article->setUser($user);
            $article->setTitle($articleTitle);
            $article->setCategory($categories);
            $article->setPublished($published);
            $article->setCreatedAt(new \DateTime());
            $article->setTextContent($articleContent);

            if ($miniatureFile) {
                $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $miniatureFile->guessExtension();

                $miniatureFile->move($miniatureDirectory, $newFileName);

                $article->setMiniature($newFileName);
            }

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_new_article');
        }
        return $this->render('new_article/index.html.twig', []);
    }
}
