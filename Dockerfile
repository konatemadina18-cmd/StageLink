# Image de base PHP 8.3 avec les extensions nécessaires
FROM php:8.3-cli

# Installer les dépendances système et extensions PHP nécessaires pour Laravel + MySQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tout le projet dans le conteneur
COPY . .

# Installer les dépendances PHP (sans les paquets de dev, optimisé pour la prod)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Générer la clé d'application si elle n'existe pas déjà
RUN php artisan key:generate --force || true

# Donner les bonnes permissions aux dossiers storage et bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Mettre en cache la config et les routes pour de meilleures performances
RUN php artisan config:cache && php artisan route:cache || true

# Exposer le port utilisé par Render (variable dynamique)
EXPOSE 10000

# Lancer les migrations puis démarrer le serveur Laravel sur le port fourni par Render
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}