FROM    php:5.6-apache

MAINTAINER Martin Schnabel <martin@webvariants.de>

ENV     WEB_ROOT            /app
ENV     MEMCACHE_VERSION    3.0.8
ENV     MEMCACHED_VERSION   2.2.0

RUN     sed -i "s|/var/www/html|\$\{WEB_ROOT\}|" /etc/apache2/apache2.conf \
     && sed -i "s|/var/www|\$\{WEB_ROOT\}|"      /etc/apache2/apache2.conf \
     && sed -i "s|/var/www/html|\$\{WEB_ROOT\}|" /etc/apache2/sites-enabled/000-default.conf \
     && { \
            echo '<Directory ${WEB_ROOT}/>'; \
            echo '  Options Indexes FollowSymLinks'; \
            echo '  AllowOverride All'; \
            echo '  Require all granted'; \
            echo '</Directory>'; \
        } >> /etc/apache2/sites-enabled/000-default.conf \

     && apt-get update \
     && apt-get install -y \
            locales \
            libmcrypt4 \
            libmcrypt-dev \
            libcurl4-openssl-dev \
            libtidy-0.99-0 \
            libtidy-dev \
            zlib1g-dev \
            libicu52 \
            icu-devtools \
            libicu-dev \
            libmemcached-dev \
            curl \
            msmtp \

     && a2enmod rewrite headers expires \

     && echo "de_DE.UTF-8 UTF-8" >> /etc/locale.gen \
     && echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen \
     && echo "en_GB.UTF-8 UTF-8" >> /etc/locale.gen \
     && echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
     && echo "it_IT.UTF-8 UTF-8" >> /etc/locale.gen \
     && echo "nl_NL.UTF-8 UTF-8" >> /etc/locale.gen \
     && locale-gen \
     && /usr/sbin/update-locale LANG=en_US.UTF-8 \

     && docker-php-source extract \
     && curl https://pecl.php.net/get/memcache-${MEMCACHE_VERSION}.tgz | tar -xpz -C /usr/src/php/ext \
     && mv /usr/src/php/ext/memcache-${MEMCACHE_VERSION} /usr/src/php/ext/memcache \
     && curl https://pecl.php.net/get/memcached-${MEMCACHED_VERSION}.tgz | tar -xpz -C /usr/src/php/ext \
     && mv /usr/src/php/ext/memcached-${MEMCACHED_VERSION} /usr/src/php/ext/memcached \

     && docker-php-ext-install \
            memcache \
            memcached \
            tidy \
            iconv \
            curl \
            zip \
            mbstring \
            intl \
            mcrypt \
            mysql \
            pdo_mysql \

     && apt-get remove -y \
            libmcrypt-dev \
            libcurl4-openssl-dev \
            libtidy-dev \
            zlib1g-dev \
            libicu-dev \
            libmemcached-dev \

     && php -r "readfile('https://getcomposer.org/installer');" > /bin/composer-setup.php && php /bin/composer-setup.php --install-dir=/bin \

     && rm -r /usr/share/doc \
     && rm -r /var/lib/apt/lists/*

COPY    sendmail.ini               /usr/local/etc/php/conf.d/
COPY    php-policat.ini              /usr/local/etc/php/conf.d/
COPY    apache2-foreground-user    /usr/local/bin/

WORKDIR /app

CMD     ["apache2-foreground-user"]
