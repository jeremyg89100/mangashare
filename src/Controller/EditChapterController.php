<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Page;
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

final class EditChapterController extends AbstractController
{
    #[Route('/edit/chapter/{id}', name: 'app_edit_chapter')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Chapter $chapter,
        Request $request,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/pages')]
        string $pageDirectory
    ): Response {
        $manga = $chapter->getManga();
        $mangaId = $chapter->getManga()->getId();
        $chapterId = $chapter->getId();

        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Inconnu';

        if ($manga->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            try {
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
                $logger->info(\sprintf(
                    'EDIT CHAPTER : Le chapitre #%d du manga #%d a été modifié avec succès par l\'utilisateur %s',
                    $chapterId,
                    $mangaId,
                    $pseudo,
                ));

                return $this->redirectToRoute('app_edit_manga', ['id' => $manga->getId()]);
            } catch (\Exception $e) {
                $logger->error(\sprintf(
                    'EDIT CHAPTER ERREUR : L\'édition  du chapitre #%d au manga #%d a échoué par l\'utilisateur %s',
                    $chapterId,
                    $mangaId,
                    $pseudo,
                ), ['exception' => $e]);
            }
        }

        return $this->render('edit_chapter/index.html.twig', [
            'chapter' => $chapter,
            'manga' => $manga,
        ]);
    }
}
