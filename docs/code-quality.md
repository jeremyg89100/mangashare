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
