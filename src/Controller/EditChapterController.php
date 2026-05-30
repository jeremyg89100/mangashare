<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EditChapterController extends AbstractController
{
    #[Route('/edit/chapter/{id}', name: 'app_edit_chapter')]
    public function index(
        Chapter $chapter,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/pages')]
        string $pageDirectory
    ): Response {
        $manga = $chapter->getManga();

        if ($manga->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $chapter->setTitle($request->request->getString('chapterTitle'));

            $uploadFiles = $request->files->all('pages');

            $pageOrder = 1;

            foreach ($uploadFiles as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }
                $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                $file->move($pageDirectory, $newFilename);

                $page = new Page();
                $page->setImageUrl($newFilename);
                $page->setPageOrder($pageOrder);

                ++$pageOrder;

                $chapter->addPage($page);
                $em->persist($page);
            }

            $em->flush();

            return $this->redirectToRoute('app_edit_manga', ['id' => $manga->getId()]);
        }

        return $this->render('edit_chapter/index.html.twig', [
            'chapter' => $chapter,
            'manga' => $manga,
        ]);
    }
}
