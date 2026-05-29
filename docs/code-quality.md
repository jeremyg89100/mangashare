# Qualité de code

Ce projet utilise deux outils d'analyse, vérifiés automatiquement en CI (GitHub Actions).

> **Convention projet** : en local, toutes les commandes PHP/Composer passent par un
> conteneur Docker éphémère (aucun PHP installé sur la machine). La CI GitHub, elle,
> utilise `shivammathur/setup-php` — c'est l'environnement standard des Actions.
>
> Helper Docker utilisé dans cette doc :
>
> ```bash
> alias php-docker='docker run --rm -v "$PWD":/app -w /app -e COMPOSER_HOME=/app/var/.composer -e PHP_CS_FIXER_IGNORE_ENV=1 composer:2 sh -lc'
> ```

---

## php-cs-fixer

Mise en forme automatique du code selon le standard **Symfony**.

- **Configuration** : [`.php-cs-fixer.dist.php`](../.php-cs-fixer.dist.php)
- **Règles** : `@Symfony`, `@Symfony:risky`, `@PHP84Migration`
- **Périmètre** : `src/` et `tests/`
- **CI** : [`.github/workflows/php-cs-fixer.yml`](../.github/workflows/php-cs-fixer.yml) (mode `--dry-run`, échoue si du code n'est pas formaté)

### Commandes

Vérifier (sans modifier) :

```bash
php-docker 'composer cs'
# équivaut à : vendor/bin/php-cs-fixer fix --dry-run --diff
```

Corriger automatiquement :

```bash
php-docker 'composer cs:fix'
# équivaut à : vendor/bin/php-cs-fixer fix
```

> Le cache est écrit dans `var/.php-cs-fixer.cache` (gitignoré).

---

## PHPStan

Analyse statique au niveau le plus strict, avec les extensions Symfony et Doctrine.

- **Configuration** : [`phpstan.dist.neon`](../phpstan.dist.neon)
- **Niveau** : `max` (analysé pour PHP 8.4 via `phpVersion`)
- **Extensions** (auto-enregistrées par `phpstan/extension-installer`) :
  - `phpstan/phpstan-symfony` — connaît le conteneur de services (lit `var/cache/dev/App_KernelDevDebugContainer.xml`)
  - `phpstan/phpstan-doctrine` — analyse les entités/repositories (via `tests/object-manager.php`)
  - `phpstan/phpstan-strict-rules` + `phpstan/phpstan-deprecation-rules`
- **CI** : [`.github/workflows/phpstan.yml`](../.github/workflows/phpstan.yml)

### Pré-requis avant analyse

PHPStan a besoin du **conteneur compilé** (extension Symfony). Il faut donc réchauffer
le cache au préalable :

```bash
php-docker 'php bin/console cache:warmup --env=dev && composer stan'
```

> **Connexion DB** : l'analyse ne se connecte à aucune base (Doctrine est paresseux).
> Le `DATABASE_URL` doit seulement être *syntaxiquement* valide avec un `serverVersion`
> pour éviter que DBAL tente de détecter la version du serveur.

### Note sur les entités

L'option `doctrine.allowNullablePropertyForRequiredField: true` est activée : les entités
suivent le pattern `make:entity` (propriétés `?Type $x = null` pour l'hydratation, colonnes
NOT NULL). C'est une option **officielle** de l'extension, pas une suppression d'erreurs.

