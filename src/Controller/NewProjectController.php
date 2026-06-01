<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Page;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class NewProjectController extends AbstractController
{
    #[Route('/new/project', name: 'app_new_project')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[Autowire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {
        if ($request->isMethod('POST')) {
            $mangaTitle = $request->request->getString('mangaTitle');
            $user = $this->getUser();
            \assert($user instanceof User);
            $publication = $request->request->getBoolean('published');
            $readingDirection = $request->request->getString('reading-direction');
            $pages = $request->files->all('pages');
            $synopsis = $request->request->getString('synopsis');
            $chapterTitle = $request->request->getString('chapterTitle');
            $miniatureFile = $request->files->get('miniature');
            /** @var array<int, string> $categories */
            $categories = $request->request->all('categories');
            $status = $request->request->getString('status');

            $manga = new Manga();
            $manga->setTitle($mangaTitle);
            $manga->setReadingDirection($readingDirection);
            $manga->setSynopsis($synopsis);
            $manga->setStatus($status);
            $manga->setCategories($categories);
            $manga->setViews(0);
            $manga->setCreatedAt(new \DateTimeImmutable());
            $manga->setUser($user);

            $chapter = new Chapter();
            $chapter->setTitle($chapterTitle);
            $chapter->setNumber(1);
            $chapter->setPublished($publication);
            $chapter->setCreatedAt(new \DateTimeImmutable());

            if ($miniatureFile instanceof UploadedFile) {
                $originalFileName = pathinfo($miniatureFile->getClientOriginalName(), \PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$miniatureFile->guessExtension();

                $miniatureFile->move($miniatureDirectory, $newFileName);

                $manga->setMiniature($newFileName);
            }

            $pageOrder = 1;
            foreach ($pages as $pageFile) {
                if (!$pageFile instanceof UploadedFile) {
                    continue;
                }
                $originalPageName = pathinfo($pageFile->getClientOriginalName(), \PATHINFO_FILENAME);
                $safePageName = $slugger->slug($originalPageName);
                $newPageName = $safePageName.'-'.uniqid().'.'.$pageFile->guessExtension();

                $pageFile->move($pageDirectory, $newPageName);

                $page = new Page();
                $page->setImageUrl($newPageName);
                $page->setPageOrder($pageOrder);
                ++$pageOrder;
                $chapter->addPage($page);
            }

            $manga->addChapter($chapter);
            $em->persist($chapter);
            $em->persist($manga);
            $em->flush();
            $this->addFlash('success', 'Le projet a bien été enregistré !');

            return $this->redirectToRoute('app_new_project');
        }

        return $this->render('new_project/index.html.twig', []);
    }
}
