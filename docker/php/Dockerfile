# Image PHP CLI dédiée aux tests (PHPUnit + Panther).
# Contient PHP 8.4, les extensions Symfony/Doctrine et Chromium pour Panther (headless).
FROM php:8.4-cli

# Dépendances système : libs pour intl/zip/pgsql + Chromium et son driver pour Panther.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libicu-dev libzip-dev libpq-dev \
        chromium chromium-driver \
    && docker-php-ext-install -j"$(nproc)" intl zip pdo_pgsql opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer depuis l'image officielle.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Panther : on pointe explicitement sur les binaires Chromium installés ci-dessus.
ENV PANTHER_CHROME_BINARY=/usr/bin/chromium \
    PANTHER_CHROME_DRIVER_BINARY=/usr/bin/chromedriver \
    PANTHER_NO_SANDBOX=1

WORKDIR /app
