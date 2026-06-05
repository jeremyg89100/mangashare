<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Notification;
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

final class AddChapterController extends AbstractController
{
    #[Route('/add/chapter/{id}', name: 'app_add_chapter')]
    public function index(Request $request, SluggerInterface $slugger, Manga $manga, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[Autowire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {
        if ($request->isMethod('POST')) {
            $publication = $request->request->getBoolean('published');
            $pages = $request->files->all('pages');
            $chapterTitle = $request->request->getString('chapterTitle');

            $chapter = new Chapter();
            $chapter->setTitle($chapterTitle);
            $chapterNumber = \count($manga->getChapters()) + 1;
            $chapter->setNumber($chapterNumber);
            $chapter->setPublished($publication);
            $chapter->setCreatedAt(new \DateTimeImmutable());

            $pageOrder = 1;
            foreach ($pages as $pageFile) {
                if (!$pageFile instanceof UploadedFile) {
                    continue;
                }
                $originalPageName = pathinfo($pageFile->getClientOriginalName(), \PATHINFO_FILENAME);
                $safePageName = $slugger->slug($originalPageName);
                $newPageName = $safePageName . '-' . uniqid() . '.' . $pageFile->guessExtension();

                $pageFile->move($pageDirectory, $newPageName);

                $page = new Page();
                $page->setImageUrl($newPageName);
                $page->setPageOrder($pageOrder);
                ++$pageOrder;
                $chapter->addPage($page);
            }

            $manga->addChapter($chapter);

            $em->persist($manga);

            $mangaLikes = $manga->getLikes();

            $url = $this->generateUrl('app_info_manga', [
                'id' => $manga->getId(),
            ]);

            foreach ($mangaLikes as $like) {
                $follower = $like->getUser();

                $notification = new Notification();
                $notification->setCreatedAt(new \DateTimeImmutable());
                $notification->setHasBeenRead((bool) 0);
                $notification->setUser($follower);
                $notification->setMessage("\"{$manga->getTitle()}\" a publié un nouveau chapître");
                $notification->setLink($url);

                $em->persist($notification);
            }

            $em->flush();
            $this->addFlash('success', 'Le projet a bien été enregistré !');

            return $this->redirectToRoute('app_edit_manga', ['id' => $manga->getId()]);
        }

        return $this->render('add_chapter/index.html.twig', [
            'manga' => $manga,
        ]);
    }
}
