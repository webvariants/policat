FROM node:8 as assets

WORKDIR /app

COPY package.json .

RUN npm install --quiet --no-cache

COPY .bowerrc bower.json /app/

RUN npm run bower install

COPY Gruntfile.js /app/

RUN npm run grunt

FROM webvariants/php:7.2-fpm-alpine

COPY composer.json composer.lock /app/

RUN composer.phar install --no-ansi --prefer-dist --optimize-autoloader && rm -rf /root/.composer

COPY ./ /app/

RUN cd config && \
    ln -s ../data/config/app.yml app.yml && \
    ln -s ../data/config/databases.yml databases.yml && \
    ln -s ../data/config/factories.yml factories.yml && \
    ln -s ../data/config/properties.ini properties.ini && \
    ln -s ../data/config/settings.yml settings.yml

COPY --from=assets /app/web /app/web

ENV PHP_IMAGE_VERSION=2 \
    WEB_ROOT=/app/web \
    PHP_MEMORY_LIMIT=256M \
    PHPINI_SESSION__GC_MAXLIFETIME=3600 \
    PHPINI_OPCACHE__VALIDATE_TIMESTAMPS=0

USER www-data:www-data