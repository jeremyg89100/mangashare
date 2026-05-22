<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Manga;
use App\Entity\Chapter;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class NewProjectController extends AbstractController
{
    #[Route('/new/project', name: 'app_new_project')]
    public function index(Request $request,SluggerInterface $slugger, EntityManagerInterface $em, #[AutoWire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[AutoWire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {   
        if ($request->isMethod('POST')) {
            $mangaTitle = $request->request->get('mangaTitle');
            $user = $this->getUser();
            $publication = $request->request->get('published');
            $readingDirection = $request->request->get('reading-direction');
            $pages = $request->files->get('pages');
            $synopsis = $request->request->get('synopsis');
            $chapterTitle = $request->request->get('chapterTitle');
            $miniatureFile = $request->files->get('miniature');
            $categories = $request->request->all('categories');
            $status = $request->request->get('status');

            $manga = new Manga();
            $manga->setTitle($mangaTitle);
            $manga->setReadingDirection($readingDirection);
            $manga->setSynopsis($synopsis);
            $manga->setStatus($status);
            $manga->setCategories($categories);
            $manga->setViews(0);
            $manga->setCreatedAt(new \DateTime());
            $manga->setUser($user);

            $chapter = new Chapter();
            $chapter->setTitle($chapterTitle);
            $chapter->setNumber(1);
            $chapter->setPublished($publication);
            $chapter->setCreatedAt(new \DateTime());

            if ($miniatureFile) {
                $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$miniatureFile->guessExtension();

                $miniatureFile->move($miniatureDirectory, $newFileName);
                
                $manga->setMiniature($newFileName);
            }

            if ($pages) {
            $pageOrder = 1;
            foreach ($pages as $pageFile){
                $originalPageName = pathinfo($pageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safePageName = $slugger->slug($originalPageName);
                $newPageName = $safePageName.'-'.uniqid().'.'.$pageFile->guessExtension();

                $pageFile->move($pageDirectory, $newPageName);

                $page = new Page();
                $page->setImageUrl($newPageName);
                $page->setPageOrder($pageOrder);
                $pageOrder++;
                $chapter->addPage($page);}
            }


            $manga->addChapter($chapter);

            $em->persist($manga);
            $em->flush();
            $this->addFlash('success', 'Le projet a bien été enregistré !');
            return $this->redirectToRoute('app_new_project');
        }
        
        return $this->render('new_project/index.html.twig', []);
    }
}
