FROM php:8.4-cli

# Instala dependências do sistema e bibliotecas para extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala o Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instala Node.js 22 LTS (para assets e Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

EXPOSE 8000 5173

# Cria um usuário com o mesmo UID/GID padrão do WSL2 (1000)
RUN groupadd -g 1000 laravel \
    && useradd -u 1000 -g laravel -m -s /bin/bash laravel

USER laravel

EXPOSE 8000 5173
