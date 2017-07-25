# Develop policat with docker (not production)

You can use docker to develop policat. To start policat run:

    ./docker-develop 80 81

This runs mysql, memcached, maildev and php-apache with docker. Press ``Ctrl-C`` to stop it. While the server is running you can execute commands inside with ``./docker-develop-exec [command]``. You can even start a shell with ``./docker-develop-exec bash``.

The Webserver runs on port 80 so you can connect to with http://localhost/. A frontend to the test mailserver runs on http://localhost:81/.
If you are using docker with a jwilder/nginx-proxy or traefik load balancer you can start the docker environment with hostnames instead of port numbers:

    ./docker-develop policat-example.com mail-example.com

## first time setup

    composer.phar install                                                        # https://getcomposer.org/
    ./docker-develop-exec php symfony doctrine:build-db                          # create database
    ./docker-develop-exec php symfony doctrine:insert-sql                        # create tables
    ./docker-develop-exec php symfony doctrine:data-load --application=frontend  # load fixture data
    bower install                                                                # http://bower.io/
    npm install
    grunt                                                                        # http://gruntjs.com/

Go to http://localhost/guard/login and login with admin / admin.

## Symfony

Policat is based on Symfony 1.4 (legacy) http://symfony.com/legacy/doc . For API check http://www.symfony-project.org/api/ and http://doctrine.readthedocs.io/

### clear symfony cache

    ./docker-develop-exec php symfony cc

## cron tasks

To test sending of emails. You must run the following task. It will send the emails from the policat
mail queue to the mailserver:

    ./docker-develop-exec php symfony project:send-emails --application=frontend

Other tasks must be called for some features:

    ./docker-develop-exec php symfony policat:geo-cron-loop
    ./docker-develop-exec php symfony policat:delete-pending --limit=1000 --silent=1
    ./docker-develop-exec php symfony policat:recall-pending --limit=1000 --silent=1
    ./docker-develop-exec php symfony policat:action-schedule --utc-hour=00 --silent=1
    ./docker-develop-exec php symfony policat:quota-check

## common development tasks

    ./docker-develop-exec php symfony doctrine:generate-migrations-diff --application=frontend --env=dev
    ./docker-develop-exec php symfony doctrine:build --all-classes --sql
    ./docker-develop-exec php symfony doctrine:migrate
    nano data/fixtures/fixtures.yml
