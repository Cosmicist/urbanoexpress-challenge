FROM php:8.4-fpm

# Update package lists and install Git
RUN apt-get update && \
    apt-get install -y unzip libzip-dev libpq-dev && \
    docker-php-ext-install zip pdo pdo_mysql pdo_pgsql pgsql && \
    rm -rf /var/lib/apt/lists/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN addgroup --gid 1000 appgroup && adduser --ingroup appgroup --uid 1000 appuser
RUN chown -R appuser:appgroup /var/www

WORKDIR /var/www
