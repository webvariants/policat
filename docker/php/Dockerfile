FROM debian:jessie
MAINTAINER Martin Schnabel <mcnilz@gmail.com>

RUN apt-get update && apt-get -y upgrade

RUN DEBIAN_FRONTEND=noninteractive apt-get -y install \
	apache2 \
	apache2-mpm-worker \
	libapache2-mod-fcgid \
	php5-cgi \
	php5-mysql \
	php5-curl \
	php5-mcrypt \
	php5-memcache \
	php5-memcached \
	php5-tidy \ 
	php5-intl

RUN a2enmod fcgid rewrite headers expires
RUN php5enmod mcrypt
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php5/cgi/php.ini

ADD apache2.conf /etc/apache2/apache2.conf

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND
