<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\MangaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(
        Request $request,
        MangaRepository $mangaRepository,
        ArticleRepository $articleRepository,
        UserRepository $userRepository
    ): JsonResponse {

        $query = $request->query->get('q', '');

        if (\strlen($query) < 2) {
            return $this->json([]);
        }

        $mangas = $mangaRepository->search($query, 5);
        $articles = $articleRepository->search($query, 5);
        $users = $userRepository->search($query, 5);

        return $this->json([
            'mangas' => array_map(fn($m) => [
                'id' => $m->getId(),
                'title' => $m->getTitle(),
                'miniature' => $m->getMiniature(),
            ], $mangas),
            'articles' => array_map(fn($a) => [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
            ], $articles),
            'users' => array_map(fn($u) => [
                'id' => $u->getId(),
                'pseudo' => $u->getPseudo(),
                'avatar' => $u->getAvatar(),
            ], $users),
        ]);
    }
}
