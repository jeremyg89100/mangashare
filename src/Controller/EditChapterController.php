<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Page;
use App\Entity\User;
use App\Form\EditChapterType;
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

        $form = $this->createForm(EditChapterType::class, $chapter);
        $form->handleRequest($request);

        if ($manga->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user instanceof User) {
                return $this->redirectToRoute('app_login');
            }
            try {
                $pageFiles = $form->get('pagesFiles')->getData();
                $pageOrder = \count($chapter->getPages()) + 1;

                if (\is_array($pageFiles)) {
                    foreach ($pageFiles as $file) {
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
            'form' => $form->createView(),
        ]);
    }
}
