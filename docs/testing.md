# Tests

La suite de tests est organisée en **trois niveaux**, chacun exposé comme une
_testsuite_ PHPUnit distincte (voir `phpunit.dist.xml`).

| Niveau          | Dossier            | Outils                                     | Besoin DB / navigateur |
| --------------- | ------------------ | ------------------------------------------ | ---------------------- |
| **Unitaire**    | `tests/Unit`       | PHPUnit `TestCase`                         | Aucun (logique pure)   |
| **Fonctionnel** | `tests/Functional` | `WebTestCase` / `KernelTestCase` + Foundry | Base de test           |
| **Interface**   | `tests/Interface`  | Panther (Chromium headless)                | Base + navigateur      |

## Pré-requis : tout passe par Docker

Aucun PHP n'est requis sur la machine hôte. L'image `docker/php` contient PHP 8.4,
les extensions Doctrine et **Chromium + chromedriver** pour Panther.

```bash
# Construire l'image de test (une fois)
docker compose build php

# Lancer la base de données
docker compose up -d database

# Créer la base de test + le schéma (une fois, ou après un changement d'entité)
docker compose run --rm php composer test-db-reset
```

> La base de test est `app_test` (PostgreSQL). Le `DATABASE_URL` est défini dans
> `.env.test` ; le suffixe `_test` est ajouté par `config/packages/doctrine.yaml`.

## Lancer les tests

```bash
# Toute la suite
docker-compose run --rm php bin/phpunit
# ou
docker-compose run --rm php composer test

# Un seul niveau
docker-compose run --rm php bin/phpunit --testsuite=Unit
docker-compose run --rm php bin/phpunit --testsuite=Functional
docker-compose run --rm php bin/phpunit --testsuite=Interface

# Un seul fichier
docker-compose run --rm php bin/phpunit tests/Unit/Entity/MangaTest.php
```

## Quel niveau pour quoi ?

- **Unitaire** : une méthode/classe isolée, sans dépendance (ex. `Manga::isLikedByUser`).
  Rapide, à privilégier pour toute logique métier pure.
- **Fonctionnel** : un comportement de bout en bout côté serveur — un controller qui
  répond en HTTP (`HomeControllerTest`) ou un repository contre une vraie base
  (`ViewRepositoryTest`).
- **Interface** : la page telle que la voit l'utilisateur dans un vrai navigateur,
  JavaScript exécuté (`HomePageTest`).

## Isolation de la base

`dama/doctrine-test-bundle` enveloppe **chaque test fonctionnel dans une transaction**
annulée à la fin → la base reste propre entre les tests, sans recharger de fixtures.

⚠️ **Panther fait exception** : il lance un serveur web dans un **process séparé**, qui
ne voit pas la transaction du test. Les tests d'interface qui dépendent de données
doivent donc _committer_ leurs données puis nettoyer manuellement (`tearDown`). Les
exemples d'interface fournis n'assertent que du contenu statique pour rester simples.

## Données de test : Foundry

Les entités de test sont créées via des _factories_ dans `tests/Factory`. Chaque
factory définit des valeurs par défaut pour **tous les champs non-nullables** de
l'entité (sinon l'insert échoue).

```php
use App\Tests\Factory\MangaFactory;

// Un manga avec les valeurs par défaut
$manga = MangaFactory::createOne();

// En surchargeant certains champs
$manga = MangaFactory::createOne(['title' => 'Naruto']);

// Plusieurs d'un coup
MangaFactory::createMany(5);
```

### Ajouter une factory

1. Créer `tests/Factory/MaTableFactory.php` étendant `PersistentObjectFactory`.
2. Implémenter `class()` (FQCN de l'entité) et `defaults()` (valeurs des champs
   non-nullables).
3. Référencer une autre factory pour les relations : `'user' => UserFactory::new()`.

> Sous Symfony 8, on utilise `PersistentObjectFactory` (et **non**
> `PersistentProxyObjectFactory`, désormais interdit).
