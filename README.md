# MangaShare

Application Symfony 8 / PHP 8.4 de partage de mangas et d'articles.

## Stack

- Symfony 8.0 · PHP 8.4 · Doctrine ORM 3
- Asset Mapper · Stimulus · Turbo · Twig
- PostgreSQL + Mailpit via Docker Compose

## Démarrage

```bash
docker compose up -d           # PostgreSQL + Mailpit
symfony serve                  # ou le serveur PHP de votre choix
```

## Qualité de code

Le projet est analysé par **php-cs-fixer** et **PHPStan** (CI GitHub Actions).
Voir [`docs/code-quality.md`](docs/code-quality.md).
