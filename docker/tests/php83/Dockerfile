# Setup php, apache and stud.ip
FROM php:8.3-cli

# Install system requirements
RUN apt update && apt install -y  --no-install-recommends \
    default-mysql-client default-libmysqlclient-dev \
    zlib1g-dev libpng-dev libjpeg-dev libonig-dev libzip-dev libicu-dev \
    lsb-release \
    zip \
    tar \
    libwebp-dev \
    && rm -rf /var/lib/apt/lists/*

# Install php extensions
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-webp
RUN docker-php-ext-install -j$(nproc) gd gettext intl mysqli pdo_mysql zip

# Install node/npm
COPY --from=node:18-slim /usr/local/bin /usr/local/bin

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
