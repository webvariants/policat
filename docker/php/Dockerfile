FROM    php:7.2-apache

MAINTAINER Martin Schnabel <martin@webvariants.de>

ENV     WEB_ROOT            /app
ENV     MEMCACHED_VERSION   3.1.3

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
     && echo "force-unsafe-io" > /etc/dpkg/dpkg.cfg.d/02apt-speedup \
     && echo "Acquire::http {No-Cache=True;};" > /etc/apt/apt.conf.d/no-cache \
     && export MAKEFLAGS="-j $(grep -c ^processor /proc/cpuinfo)" \
     && apt-get update \
     && apt-get install -y \
            locales \
            libcurl3 \
            libcurl4-openssl-dev \
            libtidy5 \
            libtidy-dev \
            zlib1g \
            zlib1g-dev \
            libicu57 \
            icu-devtools \
            libicu-dev \
            libmemcached11 \
            libmemcached-dev \
            curl \
            msmtp \
            libfreetype6 \
            libfreetype6-dev \
            libjpeg62-turbo \
            libjpeg62-turbo-dev \
            libpng16-16 \
            libpng-dev \
            libxml2-dev \

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
     && curl https://pecl.php.net/get/memcached-${MEMCACHED_VERSION}.tgz | tar -xpz -C /usr/src/php/ext \
     && mv /usr/src/php/ext/memcached-${MEMCACHED_VERSION} /usr/src/php/ext/memcached \

     && docker-php-ext-install \
            memcached \
            gd \
            tidy \
            iconv \
            curl \
            zip \
            mbstring \
            intl \
            pdo_mysql \
            soap \

     && apt-get remove -y \
            libcurl4-openssl-dev \
            libtidy-dev \
            zlib1g-dev \
            libicu-dev \
            libmemcached-dev \
            libfreetype6-dev \
            libjpeg62-turbo-dev \
            libpng-dev \

     && php -r "readfile('https://getcomposer.org/installer');" > /bin/composer-setup.php && php /bin/composer-setup.php --install-dir=/bin \

     && rm -r /usr/share/doc \
     && rm -r /var/lib/apt/lists/*

COPY    sendmail.ini               /usr/local/etc/php/conf.d/
COPY    php-policat.ini              /usr/local/etc/php/conf.d/
COPY    apache2-foreground-user    /usr/local/bin/

WORKDIR /app

CMD     ["apache2-foreground-user"]
