# sfCacheTaggingPlugin

The ``sfCacheTaggingPlugin`` is a ``Symfony`` plugin, that helps to store cache with
associated tags and to keep cache content up-to-date based by incrementing tag
version when cache objects are edited/removed or new objects are ready to be a
part of cache content.

# Table of contents

 * <a href="#desc">Description</a>
 * <a href="#install">Installation</a>
 * <a href="#quick-setup">Quick setup</a>
 * <a href="#usage">Usage</a>
 * <a href="#advanced-setup">Advanced setup</a>
 * <a href="#misc">Miscellaneous</a>

# <a id="desc">Description</a>

Tagging a cache is a concept that was invented in the same time by many developers
([Andrey Smirnoff](http://www.smira.ru), [Dmitryj Koteroff](http://dklab.ru/)
and, perhaps, by somebody else)

This software was developed inspired by Andrey Smirnoff's theoretical work
["Cache tagging with Memcached (on Russian)"](http://www.smira.ru/tag/memcached/).
Some ideas are implemented in the real world (e.i. tag versions based on datetime
and micro time, cache hit/set logging, cache locking) and part of them
are not (atomic counter).

# <a id="install">Installation</a>

 * As Symfony plugin

  * Installation

            $ ./symfony plugin:install sfCacheTaggingPlugin

  * Upgrading

            $ ./symfony cc
            $ ./symfony plugin:upgrade sfCacheTaggingPlugin

 * As a git submodule (master or devel branch)

  * Installation

            $ git submodule add git://github.com/fruit/sfCacheTaggingPlugin.git plugins/sfCacheTaggingPlugin
            $ git submodule init plugins/sfCacheTaggingPlugin

  * Upgrading

            $ cd plugins/sfCacheTaggingPlugin
            $ git pull origin master
            $ cd ../..

 * Migrating

        $ ./symfony doctrine:migrate-generate-diff

# <a id="quick-setup">Quick setup</a>

  _After quick setup you may be interested in "<a href="#advanced-setup">Advanced setup</a>"_

## 1. Check plugin is enabled.

Location: ``/config/ProjectConfiguration.class.php``

    [php]
    <?php

    class ProjectConfiguration extends sfProjectConfiguration
    {
      public function setup ()
      {
        # … other plugins
        $this->enablePlugins('sfCacheTaggingPlugin');
      }
    }

## 2. Change default model class

This will switch default model class ``sfDoctineRecord`` with ``sfCachetaggableDoctrineRecord``

    [php]
    <?php

    class ProjectConfiguration extends sfProjectConfiguration
    {
      # …

      public function configureDoctrine (Doctrine_Manager $manager)
      {
        sfConfig::set(
          'doctrine_model_builder_options',
          array('baseClassName' => 'sfCachetaggableDoctrineRecord')
        );
      }
    }

Then rebuild your models:

    $ ./symfony doctrine:build-model

## 3. Configure "_view_cache_" and "_view_cache_manager_" in ``/config/factories.yml``

    all:
      view_cache_manager:
        class: sfViewCacheTagManager

      view_cache:
        class: sfTaggingCache
        param:
          storage:
            class: sfFileTaggingCache
            param:
              automatic_cleaning_factor: 0
              cache_dir: %SF_CACHE_DIR%/sf_tag_cache
          logger:
            class: sfFileCacheTagLogger
            param:
              file: %SF_LOG_DIR%/cache_%SF_ENVIRONMENT%.log
              format: "%char% %microtime% %key%%EOL%"



## 4. Add "_Cachetaggable_" behavior to the each model you want to cache

    Article:
      tableName: articles
      actAs:
        Cachetaggable: ~

And don't forget to rebuild models again:

    $ ./symfony doctrine:build-model

## 5. Enable cache and declare required helpers in ``/apps/%APP%/config/settings.yml``:

    dev:
      .settings:
        cache: true

    all:
      .settings:
        standard_helpers:
          - Partial
          - Cache

# <a id="usage">Usage</a>

## How to cache partials?

  * Enable cache in ``/apps/%APP%/modules/%MODULE%/config/cache.yml``:

        _listing:
          enabled: true

  * Action template ``indexSuccess.php``:

        [php]
        <?php /* @var $articles Doctrine_Collection_Cachetaggable */ ?>

        <h1><?php __('Articles') ?></h1>
        <?php include_partial('articles/listing', array(
          'articles' => $articles,
          'sf_cache_tags' => $articles,
        )) ?>

## How to cache components? (one-table)

  * ``components.class.php``

        [php]
        <?php

        class articlesComponents extends sfComponents
        {
          public function executeListOfArticles ($request)
          {
            /* @var $articles Doctrine_Collection_Cachetaggable */
            $articles = Doctrine::getTable('Article')
              ->createQuery('a')
              ->select('a.*')
              ->orderBy('a.id DESC')
              ->limit(3)
              ->execute();

            $this->setContentTags($articles);

            $this->articles = $articles;
          }
        }

  * Action template: ``indexSuccess.php``

        [php]
        <fieldset>
          <legend>Articles inside component</legend>
          <?php include_component('articles', 'listOfArticles'); ?>
        </fieldset>

  * Enable component caching in ``/apps/%APP%/modules/%MODULE%/config/cache.yml``:

        _listOfArticles:
          enabled: true

## How to cache components? (many-table, combining articles and comments 1:M relation)

  * ``components.class.php``

        [php]
        <?php

        class articlesComponents extends sfComponents
        {
          public function executeListOfArticlesAndComments($request)
          {
            $articles = Doctrine::getTable('Article')
              ->createQuery('a')
              ->addSelect('a.*, ac.*')
              ->innerJoin('a.ArticleComments ac')
              ->orderBy('a.id DESC')
              ->limit(3)
              ->execute();

            $this->setContentTags($articles);

            $this->articles = $articles;
          }
        }

  * ``indexSuccess.php``

        [php]
        <fieldset>
          <legend>Component (articles and comments)</legend>
          <?php include_component('article', 'listOfArticlesAndComments'); ?>
        </fieldset>

  * Enable component caching in ``/apps/%APP%/modules/%MODULE%/config/cache.yml``

        _listOfArticlesAndComments:
          enabled: true


## How to cache action with layout?

  * Controller example:

        [php]
        <?php

        class carActions extends sfActions
        {
          public function executeShow (sfWebRequest $request)
          {
            $car = Doctrine::getTable('car')
              ->find($request->getParameter('id'));

            $driver = Doctrine::getTable('driver')
              ->find($request->getParameter('driverId'));

            $this->setContentTags($car);
            $this->addContentTags($driver);

            $this->car = $car;
            $this->driver = $driver;
          }
        }

  * Enable caching in ``/apps/%APP%/modules/%MODULE%/config/cache.yml``:

        showSuccess:
          with_layout: true
          enabled:     true

## How to cache action _without_ layout?

  * Action example

        [php]
        <?php

        class carActions extends sfActions
        {
          public function executeShow (sfWebRequest $request)
          {
            $car = Doctrine::getTable('car')->find($request->getParameter('id'));

            $this->setContentTags($car);

            $this->car = $car;
          }
        }

  * Enable cache in ``/apps/%APP%/modules/%MODULE%/config/cache.yml``:

        show:
          with_layout: false
          enabled:     true

## How to cache ``Doctrine_Record``/``Doctrine_Collection``?

  * Does not depends on ``cache.yml`` file

  * To cache objects/collection with tags you need to enable
    result cache by calling ``Doctrine_Query::useResultCache()``:

        [php]
        <?php

        class articleActions extends sfActions
        {
          public function executeArticles (sfWebRequest $request)
          {
            $articles = Doctrine::getTable('Article')
              ->createQuery()
              ->useResultCache()
              ->addWhere('lang = ?', 'en_GB')
              ->addWhere('is_visible = ?', true)
              ->limit(15)
              ->execute();

            $this->articles = $articles;
          }
        }

# <a id="advanced-setup">Advanced setup</a>

_NB. Please read "<a href="#quick-setup">Quick setup</a>" before reading this._

## How to cache private blocks (actions/pages/partials) for authenticated users

  Symfony's cache mechanism creates the unique key to each block you want to cache based on
  following arguments:

    - Module name
    - Action name
    - $_GET arguments

  In case you would like to cache user's private data you must be very careful.
  To prevent users of seeing other user private data you need to add
  additional parameter to distinguish cached blocks among other private blocks.

  The easiest way is to keep user_id/username in URL, but it's awful.
  I suggest to add custom $_GET parameter on the fly. This will
  prevent of showing "user_id" in URL.

  What should you do is to register a new filter ``AuthParamFilter`` and switch standard
  ``sfWebRequest`` with plugin's one ``sfCacheTaggingWebRequest``.

  Place ``AuthParamFilter`` before "caching" filter in ``apps/%application_name%/config/filters.yml``

    rendering: ~
    security:  ~

    auth_params:
      class: AuthParamFilter

    cache:     ~
    execution: ~

  Switch to sfCacheTaggingWebRequest in ``apps/%application_name%/config/factories.yml``

    all:
      request:
        class: sfCacheTaggingWebRequest

  That's all. Now cache content will be based on additional parameter "user_id" in case
  user have successfully authenticated.

## Explaining ``/config/factories.yml``

    all:
      view_cache_manager:
        class: sfViewCacheTagManager

      view_cache:
        class: sfTaggingCache
        param:

          # Content will be stored in Memcache
          # Here you can switch to any other backend
          # (see below "Restrictions" for more info)
          storage:
            class: sfMemcacheTaggingCache
            param:
              persistent: true
              storeCacheInfo: true
              host: localhost
              port: 11211
              lifetime: 86400

          logger:
            class: sfFileCacheTagLogger   # to disable logger, set class to "sfNoCacheTagLogger"
            param:
              # All given parameters are default
              file:         %SF_LOG_DIR%/cache_%SF_ENVIRONMENT%.log
              file_mode:    0640
              dir_mode:     0750
              time_format:  "%Y-%b-%d %T%z"   # e.i. 2010-Sep-01 15:20:58+0300
              skip_chars:   ""

              # Logging format
              # There are such available place-holders:
              #   %char%              - Operation char (see char explanation in sfCacheTagLogger::explainChar())
              #   %char_explanation%  - Operation explanation string
              #   %time%              - Time, when data/tag was accessed
              #   %key%               - Cache name or tag name with its version
              #   %microtime%         - Micro time timestamp when data/tag was accessed
              #   %EOL%               - Whether to append \n in the end of line
              #
              # (Example: "%char% %microtime% %key%%EOL%")
              format:       "%char%"

> **Restrictions**: Backend's class should be inherited from ``sfCache``
  class. Then, it should be implement ``sfTaggingCacheInterface``
  (due to a ``Doctrine`` cache engine compatibility).
  Also, it should support the caching of objects and/or arrays.

Therefor, plugin comes with additional extended backend classes:

  - ``sfAPCTaggingCache``
  - ``sfEAcceleratorTaggingCache``
  - ``sfFileTaggingCache``
  - ``sfMemcacheTaggingCache``
  - ``sfSQLiteTaggingCache``
  - ``sfXCacheTaggingCache``

And bonus one:

  - ``sfSQLitePDOTaggingCache`` (based on stand alone ``sfSQLitePDOCache``)


## Adding "Cachetaggable" behavior to the models

Two major setups to pay attention:

  * **Model setup**
    * When object tag will be invalidated
    * How object tag will stored (tag naming)
  * **Relation setup**
    * What will happen with related objects in case root-object is deleted or updated
    * Choosing cascading type (deleteTags, invalidateTags)

Explained behavior setup, file ``/config/doctrine/schema.yml``:

    Article:
      tableName: articles
      actAs:
        Cachetaggable:

          # If you have more then 1 unique column, you could pass all of them
          # as array (tag name will be based on all of them)
          # (default: [], primary keys will be auto-detected)
          uniqueColumn:    [id, is_visible]


          # cache tag will be based on 2 columns
          # (e.g. "Article:5:01", "Article:912:00")
          # matches the "uniqueColumn" column order
          # (default: "")
          uniqueKeyFormat: '%d-%02b'


          # Column name, where object version will be stored in table
          # (default: "object_version")
          versionColumn:    version_microtime


          # Option to skip object invalidation by changing listed columns
          # Useful for sf_guard_user.last_login or updated_at
          # (default: [])
          skipOnChange:
            - last_accessed


          # Invalidates or not object collection tag when any
          # record was updated (BC with v2.*)
          # Useful, when table contains rarely changed data (e.g. Countries, Currencies)
          # allowed values: true/false
          # (default: false)
          invalidateCollectionVersionOnUpdate: false


          # Useful option when model contains columns like "is_visible", "is_active"
          # updates collection tag, if one of columns was updated.
          # will not work if "invalidateCollectionVersionOnUpdate" is set to "true"
          # will not work if one of columns are in "skipOnChange" list.
          # (default: [])
          invalidateCollectionVersionByChangingColumns:
            - is_visible

      columns:
        id:
          type: integer(4)
          autoincrement: true
          primary: true
        culture_id:
          type: integer(4)
          notnull: false
          default: null
        category_id:
          type: integer(4)
          notnull: true
        slug: string(255)
        is_visible: boolean(true)
        is_moderated: boolean(false)
        last_accessed: date(25)
      relations:
        Culture:
          class: Culture
          local: culture_id
          foreign: id
          foreignAlias: Articles
          type: one
          foreignType: many
          # Cascading type chosen "invalidateTags"
          # Due to foreign key "onDelete" type is "SET NULL"
          cascade: [invalidateTags]
        Category:
          class: Category
          local: category_id
          foreign: id
          foreignAlias: Categories
          type: one
          foreignType: many
          # Cascading type chosen "deleteTags"
          # Due to foreign key "onDelete" type is "CASCADE"
          cascade: [deleteTags]

    Culture:
      tableName: cultures
      actAs:
        Cachetaggable: ~
      columns:
        id:
          type: integer(4)
          autoincrement: true
          primary: true
        lang: string(10)
        is_visible: boolean(true)
      relations:
        Articles:
          onDelete: SET NULL
          onUpdate: CASCADE

    Category:
      tableName: categories
      actAs:
        Cachetaggable: ~
      columns:
        id:
          type: integer(4)
          autoincrement: true
          primary: true
        name: string(127)
      relations:
        Articles:
          onDelete: CASCADE
          onUpdate: CASCADE

## Explained ``sfCacheTaggingPlugin`` options (file ``/config/app.yml``):

    all:
      sfCacheTagging:

        # Tag name delimiter
        # (default: ":")
        model_tag_name_separator: ":"

        # Version of precision
        # 0: without micro time, version length 10 digits
        # 5: with micro time part, version length 15 digits
        # allowed decimal numbers in range [0, 6]
        # (default: 5)
        microtime_precision: 5

        # Callable array
        # Example: [ClassName, StaticClassMethod]
        # useful when tag name should contains extra information
        # (e.g. Environment name, or application name)
        # (default: [])
        object_class_tag_name_provider: []


## Tag manipulations

Here is a list of available methods you can call inside ``sfComponent`` & ``sfAction`` to manage tags:

 - ``setContentTags (mixed $tags)``
 - ``addContentTags (mixed $tags)``
 - ``getContentTags ()``
 - ``removeContentTags ()``
 - ``setContentTag (string $tagName, string $tagVersion)``
 - ``hasContentTag (string $tagName)``
 - ``removeContentTag (string $tagName)``
 - ``disableCache (string $moduleName = null, string $actionName = null)``
 - ``addDoctrineTags (mixed $tags, Doctrine_Query $q, array $params = array())``

More about is you could find in ``sfViewCacheTagManagerBridge.class.php``

Component example:

    [php]
    <?php

    class articlesComponents extends sfComponents
    {
      public function executeList ($request)
      {
        $articles = ArticleTable::getInstance()->findAll();
        $this->setContentTags($articles);

        # Appending tags to already set $articles tags
        $banners = BannerTable::getInstance()->findByCategoryId(4);
        $this->addContentTags($articles);

        # adding only Culture collection tag "Culture"
        # useful when page contains all cultures output in form widget
        $this->addContentTags(CultureTable::getInstance());


        # adding personal tag
        $this->addContentTag('Portal_EN', sfCacheTaggingToolkit::generateVersion());

        # deleting added before tag
        $this->removeContentTag('Article:31');

        # printing all set tags, excepting removed one
        // var_dump($this->getContentTags());

        $this->articles = $articles;
        $this->banners = $banners;
      }
    }

## Configurating Doctrine`s query cache

Remember to enable Doctrine query cache in production:

    [yml]
    # config/app.yml
    dev:
      doctrine:
        query_cache: ~

    prod:
      doctrine:
        query_cache:
          class: Doctrine_Cache_Apc # or another backend class Doctrine_Cache_*
          param:
            prefix: doctrine_dql_query_cache
            lifetime: 86400

And plug in query cache:

    [php]
    <?php

    class ProjectConfiguration extends sfProjectConfiguration
    {
      public function configureDoctrine (Doctrine_Manager $manager)
      {
        $doctrineQueryCache = sfConfig::get('app_doctrine_query_cache');

        if ($doctrineQueryCache)
        {
          list($class, $param) = array_values($doctrineQueryCache);
          $manager->setAttribute(
            Doctrine_Core::ATTR_QUERY_CACHE,
            new $class($param)
          );

          if (isset($param['lifetime']))
          {
            $manager->setAttribute(
              Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN,
              (int) $param['lifetime']
            );
          }
        }
      }
    }

## Clarifying  Doctrine`s result cache

Plugin contains universal proxy class ``Doctrine_Cache_Proxy`` to connect Doctrine
cache mechanisms with Symfony's one. This mean, when you setup "storage" cache back-end to
file cache, [Doctrine`s result cache](http://www.doctrine-project.org/projects/orm/1.2/docs/manual/caching/en#query-cache-result-cache:result-cache) will use it to store cached ``DQL`` results.

To enable result cache use:

    $q->useResultCache();

Set hydration to ``Doctrine_Core::HYDRATE_RECORD`` (NB! using another hydrator, its impossible to cache ``DQL`` result with tags.)

    [php]
    <?php

    $q
      ->setHydrationMode(Doctrine_Core::HYDRATE_RECORD)
      ->execute();

    // or
    $q->execute(array(), Doctrine_Core::HYDRATE_RECORD);

Cached ``DQL`` results will be associated with all linked tags based on query results.

# <a id="misc">Miscellaneous</a>

## New in v4.1.1:

  * [Removed] Removing from package test files - all test environment located in GIT repository

## Limitations / Specificity

  * In case, when model has translations (I18n behavior), it is enough to add
    ``Cachetaggable`` behavior to the root model. I18n behavior should be free from ``Cachetaggable`` behavior.
  * You can't pass ``I18n`` table columns to the ``skipOnChange``.
  * Doctrine ``$q->count()`` can't be cached with tags
  * Be careful with joined I18n tables, cached result may differs from the expected.
    Due the [unresolved ticket](http://trac.symfony-project.org/ticket/7220) it *could be* impossible.

## TDD

  * Environment: PHP 5.3.8
  * Unit tests: 12
  * Functional tests: 31
  * Checks: 1340
  * Code coverage: 95%

Whether you want to run a plugin tests, you need:

  1. Install plugin from GIT repository.
  2. Install [APC](http://pecl.php.net/package/APC) and [Memcache](http://pecl.php.net/package/Memcache)
  3. Configure ``php.ini`` and restart Apache/php-fpm:

        [ini]
        [APC]
          apc.enabled = 1
          apc.enable_cli = 1
          apc.use_request_time = 0

  4. Add CLI variable:

    For current session only:

        $ export SYMFONY=/path/to/symfony/lib

    For all further sessions:

        $ echo "export SYMFONY=/path/to/symfony/lib" >> ~/.bashrc

  5. Run tests:

        [php]
        $ cd plugins/sfCacheTaggingPlugin/test/fixtures/project/

        # it will create the ``sfcachetaggingplugin_test`` database
        $ ./symfony doctrine:build --all --and-load --env=test

        # runs unit and functional tests
        $ ./symfony test:all

        # runs all unit tests
        $ ./symfony test:unit

        # runs all functional tests
        $ ./symfony test:functional


## Contribution

* [Repository (GitHub)](http://github.com/fruit/sfCacheTaggingPlugin "Repository (GitHub)")
* [Issues (GitHub)](http://github.com/fruit/sfCacheTaggingPlugin/issues "Issues")

## Contacts ##

  * @: Ilya Sabelnikov `` <fruit dot dev at gmail dot com> ``
  * skype: ilya_roll