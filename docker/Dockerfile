FROM php:8.2-fpm

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and required build tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    ca-certificates \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Configure and install required PHP extensions:
# pdo_mysql, mbstring, exif, pcntl, bcmath, gd (with freetype, jpeg, webp), zip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# Install Node.js (v20 LTS) and npm
RUN mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Align www-data UID/GID with host user (1000:1000) for seamless volume mounting
ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data || true

# Set proper permissions for home directory, composer cache, npm cache, and Laravel storage
RUN mkdir -p /var/www/.npm /var/www/.composer /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000

CMD ["php-fpm"]
