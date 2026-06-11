<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'app_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $mangas = $user->getMangas();
        $articles = $user->getArticles();

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'mangas' => $mangas,
            'articles' => $articles,
        ]);
    }

    #[Route('/dashboard/data/{type}/{id}/{metric}', name: 'app_dashboard_data', methods: ['GET'])]
    public function getData(string $type, int $id, string $metric, MangaRepository $mangaRepository, ArticleRepository $articleRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ('manga' === $type) {
            $content = $mangaRepository->find($id);

            if (null === $content) {
                return $this->json(['error' => 'Manga introuvable'], 404);
            }

            if ($content->getUser() !== $user) {
                return $this->json(['error' => 'Accès refusé'], 403);
            }
        }

        if ('article' === $type) {
            $content = $articleRepository->find($id);

            if (null === $content) {
                return $this->json(['error' => 'Article introuvable'], 404);
            }

            if ($content->getUser() !== $user) {
                return $this->json(['error' => 'Accès refusé'], 403);
            }
        }

        $data = [];
        for ($i = 6; $i >= 0; --$i) {
            $date = new \DateTimeImmutable("-{$i} days");
            $label = $date->format('d/m');

            $data[] = [
                'label' => $label,
                'value' => $this->getMetricValue($type, $id, $metric, $date, $em),
            ];
        }

        return $this->json([
            'labels' => array_column($data, 'label'),
            'values' => array_column($data, 'value'),
            'metric' => $metric,
        ]);
    }

    private function getMetricValue(string $type, int $id, string $metric, \DateTimeImmutable $date, EntityManagerInterface $em): int
    {
        dump($type, $id, $metric, $date->format('Y-m-d'));

        return match ($metric) {
            'likes' => $em->getRepository(\App\Entity\Like::class)->countByDay($id, $type, $date),
            'comments' => $em->getRepository(\App\Entity\Comment::class)->countByDay($id, $type, $date),
            'views' => $em->getRepository(\App\Entity\View::class)->countByDay($id, $type, $date),
            default => 0,
        };
    }
}
