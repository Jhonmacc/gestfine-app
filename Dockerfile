# Use uma imagem base PHP com suporte a FPM
FROM php:8.1-apache

# Instale dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libgmp-dev \
    && rm -rf /var/lib/apt/lists/*

# Instale extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install mysqli pdo pdo_mysql zip opcache \
    && docker-php-ext-install intl \
    && docker-php-ext-install bcmath

# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Copie os arquivos de configuração e código-fonte para o contêiner
COPY . /var/www/html

# Instale as dependências do projeto Laravel
RUN composer install --no-dev --optimize-autoloader

# Exponha a porta 80 para o contêiner
EXPOSE 80

# Ajuste permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configuração do Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Ativa o módulo rewrite do Apache
RUN a2enmod rewrite