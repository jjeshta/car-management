FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring exif pcntl bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*  # Clean up APT when done

# Copy the Symfony project files
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy the Symfony project files into the container
COPY ./car-mgt-service/ .
COPY ./car-mgt-service/.env . 

RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var
# Install project dependencies
# RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html
RUN chown -R www-data:www-data /var/www/html/var

# Change user and group ID for www-data
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Switch to www-data user
USER www-data

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
