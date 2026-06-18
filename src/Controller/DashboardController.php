<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard/', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $mangas = $user->getMangas();

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'mangas' => $mangas,
        ]);
    }

    #[Route('/dashboard/data/{type}/{id}/{metric}', name: 'app_dashboard_data', methods: ['GET'])]
    public function getData(string $type, int $id, string $metric, Request $request, MangaRepository $mangaRepository, ArticleRepository $articleRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Contrôle d'appartenance : un utilisateur ne consulte que les stats de SON contenu.
        if ('manga' === $type) {
            $content = $mangaRepository->find($id);
        } elseif ('article' === $type) {
            $content = $articleRepository->find($id);
        } else {
            return $this->json(['error' => 'Type de contenu invalide'], 400);
        }

        if (null === $content) {
            return $this->json(['error' => 'Contenu introuvable'], 404);
        }

        if ($content->getUser() !== $user) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        $weekParam = $request->query->get('week');
        if (null !== $weekParam) {
            [$year, $week] = explode('-W', $weekParam);
            $startOfWeek = new \DateTimeImmutable()->setISODate((int) $year, (int) $week, 1);
        } else {
            $startOfWeek = new \DateTimeImmutable('monday this week');
        }
        $data = [];
        for ($i = 0; $i < 7; ++$i) {
            $date = $startOfWeek->modify("+{$i} days");
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
        // Les likes d'articles vivent dans LikeArticle, qui ne porte pas encore de
        // date de création : impossible de compter par jour. On renvoie 0 plutôt qu'un
        // total manga erroné. Le décompte réel arrivera avec LikeArticle::createdAt.
        if ('likes' === $metric && 'article' === $type) {
            return 0;
        }

        return match ($metric) {
            'likes' => $em->getRepository(\App\Entity\Like::class)->countByDay($id, $type, $date),
            'comments' => $em->getRepository(\App\Entity\Comment::class)->countByDay($id, $type, $date),
            'views' => $em->getRepository(\App\Entity\View::class)->countByDay($id, $type, $date),
            default => 0,
        };
    }
}
