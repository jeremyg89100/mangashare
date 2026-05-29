<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class EditArticlesController extends AbstractController
{
    #[Route('/edit/articles/{id}', name: 'app_edit_articles')]
    public function index(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/miniatureArticle')] string $miniatureDirectory
    ): Response {
        if ($article->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $article->setTitle($request->request->get('articleTitle'));
            $article->setCategory($request->request->get('category'));
            $article->setPublished($request->request->get('published'));
            $article->setTextContent($request->request->get('article-content'));

            $miniatureFile = $request->files->get('miniature');


            if ($miniatureFile) {
                $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $miniatureFile->guessExtension();

                $miniatureFile->move($miniatureDirectory, $newFileName);

                $article->setMiniature($newFileName);
            }

            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_my_articles');
        }
        return $this->render('edit_articles/index.html.twig', [
            'article' => $article,
        ]);
    }
}
