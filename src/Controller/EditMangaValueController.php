<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Entity\User;
use App\Form\EditMangaType;
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

final class EditMangaValueController extends AbstractController
{
    #[Route('/edit/manga/{id}/value', name: 'app_edit_manga_value')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, Manga $manga, SluggerInterface $slugger, LoggerInterface $logger, EntityManagerInterface $em, #[Autowire('%kernel.project_dir%/public/uploads/miniatures')] string $miniatureDirectory): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($manga->getUser() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier ce manga.");
        }

        $form = $this->createForm(EditMangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $user->getPseudo();
            $mangaId = $manga->getId();

            try {
                $miniature = $form->get('miniatureFile')->getData();

                if ($miniature instanceof UploadedFile) {
                    $newMiniatureName = $slugger->slug(pathinfo($miniature->getClientOriginalName(), \PATHINFO_FILENAME)).'-'.uniqid().'.'.$miniature->guessExtension();
                    $miniature->move($miniatureDirectory, $newMiniatureName);
                    $manga->setMiniature($newMiniatureName);
                }
                $em->flush();

                $logger->info(\sprintf(
                    'MODIFICATION MANGA : Le manga #%d de l\'utilisateur #%s a été modifié avec succès',
                    $mangaId,
                    $pseudo,
                ));

                return $this->redirectToRoute('app_edit_manga', [
                    'id' => $manga->getId(),
                ]);
            } catch (\Exception $e) {
                $logger->error(\sprintf(
                    'ERREUR MODIFICATION MANGA : La modification du manga #%d de l\'utilisateur #%s a échoué',
                    $mangaId,
                    $pseudo,
                ), ['exception' => $e]);
            }
        }

        return $this->render('edit_manga_value/index.html.twig', [
            'form' => $form->createView(),
        ], new Response(
            null,
            $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK
        ));
    }
}
