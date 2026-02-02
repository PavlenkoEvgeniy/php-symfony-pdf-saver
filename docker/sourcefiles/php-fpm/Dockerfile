FROM php:8.4-fpm

WORKDIR "/application"

RUN apt-get update

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add dependencies
RUN apt-get update && apt-get install -y \
    libsnmp-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl

# Install aspell for checking grammar in project
RUN apt-get install aspell aspell-en -y

# Create download directory
RUN mkdir -p /application/var/downloads/
RUN chmod -R 775 /application/var/downloads/
RUN chown -R www-data:www-data /application/var/downloads/
