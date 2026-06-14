<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Notification;
use App\Entity\Page;
use App\Entity\User;
use App\Form\ChapterType;
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

final class AddChapterController extends AbstractController
{
    #[Route('/add/chapter/{id}', name: 'app_add_chapter')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, SluggerInterface $slugger, Manga $manga, EntityManagerInterface $em, LoggerInterface $logger, #[Autowire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[Autowire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {
        $chapter = new Chapter();
        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        $user = $this->getUser();
        $pseudo = ($user instanceof User) ? $user->getPseudo() : 'Anonyme';

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user instanceof User) {
                return $this->redirectToRoute('app_login');
            }

            try {
                $chapter->setCreatedAt(new \DateTimeImmutable());
                $chapterNumber = \count($manga->getChapters()) + 1;
                $chapter->setNumber($chapterNumber);

                $pageFiles = $form->get('pagesFiles')->getData();
                if (\is_array($pageFiles)) {
                    $pageOrder = 1;
                    foreach ($pageFiles as $pageFile) {
                        if ($pageFile instanceof UploadedFile) {
                            $newPageName = $slugger->slug(pathinfo($pageFile->getClientOriginalName(), \PATHINFO_FILENAME)).'-'.uniqid().'.'.$pageFile->guessExtension();
                            $pageFile->move($pageDirectory, $newPageName);

                            $page = new Page();
                            $page->setImageUrl($newPageName);
                            $page->setPageOrder($pageOrder++);
                            $chapter->addPage($page);
                        }
                    }
                }

                $manga->addChapter($chapter);

                $em->persist($chapter);
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

                $logger->info(\sprintf('L\'utilisateur %s a ajouté un chapitre avec succès', $pseudo));

                return $this->redirectToRoute('app_edit_manga', ['id' => $manga->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'enregistrement du chapitre.');
                $logger->error(\sprintf(
                    "Echec ajout chapitre : L'utilisateur %s a échoué a ajouté un chapitre.",
                    $pseudo
                ), ['exception' => $e]);
            }
        }

        return $this->render('add_chapter/index.html.twig', [
            'manga' => $manga,
            'form' => $form->createView(),
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK
        ));
    }
}
