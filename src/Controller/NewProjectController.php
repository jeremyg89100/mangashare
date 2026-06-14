<?php

namespace App\Controller;

use App\Entity\Chapter;
use App\Entity\Manga;
use App\Entity\Page;
use App\Entity\User;
use App\Form\MangaType;
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

final class NewProjectController extends AbstractController
{
    #[Route('/new/project', name: 'app_new_project')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, SluggerInterface $slugger, LoggerInterface $logger, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory, #[Autowire('%kernel.project_dir%/public/uploads/pages')] string $pageDirectory): Response
    {
        $manga = new Manga();
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                return $this->redirectToRoute('app_login');
            }
            $pseudo = $user->getPseudo();
            $mangaId = $manga->getId();

            try {
                $manga->setUser($user);
                $manga->setViews(0);
                $manga->setCreatedAt(new \DateTimeImmutable());

                $miniature = $form->get('miniatureFile')->getData();

                if ($miniature instanceof UploadedFile) {
                    $newMiniatureName = $slugger->slug(pathinfo($miniature->getClientOriginalName(), \PATHINFO_FILENAME)).'-'.uniqid().'.'.$miniature->guessExtension();
                    $miniature->move($miniatureDirectory, $newMiniatureName);
                    $manga->setMiniature($newMiniatureName);
                }

                $chapterForm = $form->get('firstChapter');
                /** @var Chapter $chapter */
                $chapter = $chapterForm->getData();
                $chapter->setNumber(1);
                $chapter->setCreatedAt(new \DateTimeImmutable());

                $pageFiles = $chapterForm->get('pagesFiles')->getData();
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
                $em->persist($manga);
                $em->persist($chapter);
                $em->flush();

                $logger->info(\sprintf(
                    'AJOUT NOUVEAU MANGA : Le manga #%d de l\'utilisateur #%s a été ajouté avec succès',
                    $mangaId,
                    $pseudo,
                ));

                $this->addFlash('success', 'Le projet a été enregistré');

                return $this->redirectToRoute('app_new_project');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur a été rencontré lors de la création du projet');
                $logger->error(\sprintf(
                    'ERREUR AJOUT NOUVEAU MANGA : L\'ajout du nouveau manga #%d de l\'utilisateur #%s a échoué',
                    $mangaId,
                    $pseudo,
                ), ['exception' => $e]);
            }
        }

        return $this->render('new_project/index.html.twig', [
            'form' => $form->createView(),
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK
        ));
    }
}
