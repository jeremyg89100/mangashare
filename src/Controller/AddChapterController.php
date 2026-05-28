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

final class AddChapterController extends AbstractController
{
    #[Route('/add/chapter/{id}', name: 'app_add_chapter')]
    public function index(Request $request,SluggerInterface $slugger, Manga $manga, EntityManagerInterface $em, #[AutoWire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[AutoWire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {   
        if ($request->isMethod('POST')) {
            $publication = $request->request->get('published');
            $pages = $request->files->get('pages');
            $chapterTitle = $request->request->get('chapterTitle');

            $chapter = new Chapter();
            $chapter->setTitle($chapterTitle);
            $chapterNumber = count($manga->getChapters()) +1;
            $chapter->setNumber($chapterNumber);
            $chapter->setPublished($publication);
            $chapter->setCreatedAt(new \DateTime());

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
            return $this->redirectToRoute('app_edit_manga', ['id' => $manga->getId()]);
        }
        
        return $this->render('add_chapter/index.html.twig', [
            'manga' => $manga,
        ]);
    }
}

