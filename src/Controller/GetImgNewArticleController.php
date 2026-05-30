<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GetImgNewArticleController extends AbstractController
{
    #[Route('/upload-article-image', name: 'app_get_img_new_article')]
    public function uploadArticleImage(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $uploadFile = $request->files->get('upload');

        if (!$uploadFile) {
            return new JsonResponse(['error' => ['message' => 'Aucun fichier trouvé']], 400);
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
