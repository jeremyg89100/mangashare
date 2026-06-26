<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GetImgNewArticleController extends AbstractController
{
    private const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

    #[Route('/upload-article-image', name: 'app_get_img_new_article')]
    #[IsGranted('ROLE_USER')]
    public function uploadArticleImage(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $uploadFile = $request->files->get('upload');

        if (!$uploadFile instanceof UploadedFile) {
            return new JsonResponse(['error' => ['message' => 'Aucun fichier trouvé']], 400);
        }

        if (!\in_array($uploadFile->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            return new JsonResponse(['error' => ['message' => 'Type de fichier non autorisé (images uniquement)']], 415);
        }

        $originalFileName = pathinfo($uploadFile->getClientOriginalName(), \PATHINFO_FILENAME);
        $safeFileName = $slugger->slug($originalFileName);
        $newFileName = $safeFileName.'-'.uniqid().'.'.$uploadFile->guessExtension();

        try {
            $uploadFile->move($this->getParameter('kernel.project_dir').'/public/uploads/imgArticles', $newFileName);

            return new JsonResponse(['url' => '/uploads/imgArticles/'.$newFileName]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => ['message' => 'Erreur de la sauvegarde'], $e]);
        }
    }
}
