# Develop policat with docker (not production)

You can use docker to develop policat. To start policat run:

    docker-compose up -d

## first time setup

    docker-compose exec php composer.phar install                                  # https://getcomposer.org/
    docker-compose exec php php symfony doctrine:build-db                          # create database
    docker-compose exec php php symfony doctrine:insert-sql                        # create tables
    docker-compose exec php php symfony doctrine:data-load --application=frontend  # load fixture data
    ./build_assets                                                                 # build assets

Go to http://policat.local/ and login with admin / admin.

## Symfony

Policat is based on Symfony 1.4 (legacy) http://symfony.com/legacy/doc . For API check http://www.symfony-project.org/api/ and http://doctrine.readthedocs.io/

### clear symfony cache

    docker-compose exec php php symfony cc

## cron tasks

To test sending of emails. You must run the following task. It will send the emails from the policat
mail queue to the mailserver:

    docker-compose exec php php symfony project:send-emails --application=frontend

Other tasks must be called for some features:

    docker-compose exec php php symfony policat:geo-cron-loop
    docker-compose exec php php symfony policat:delete-pending --limit=1000 --silent=1
    docker-compose exec php php symfony policat:recall-pending --limit=1000 --silent=1
    docker-compose exec php php symfony policat:action-schedule --utc-hour=00 --silent=1
    docker-compose exec php php symfony policat:quota-check

## common development tasks

    docker-compose exec php php symfony doctrine:generate-migrations-diff --application=frontend --env=dev
    docker-compose exec php php symfony doctrine:build --all-classes --sql
    docker-compose exec php php symfony doctrine:migrate
    nano data/fixtures/fixtures.yml
