<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class frontendConfiguration extends sfApplicationConfiguration {

  public function configure() {
    $dispatcher = $this->getEventDispatcher();
    $dispatcher->connect('doctrine.configure_connection', array($this, 'configureDoctrineConnection'));
    $dispatcher->connect('task.cache.clear', array($this, 'clearQueryCache'));
  }

  private function cachePDO() {
    return new PDO('sqlite:' . realpath( __DIR__ . '/../../../cache') . '/query_cache_fe.sqlite');
  }

  private function cacheCon() {
    $pdo = $this->cachePDO();
    try {
      $pdo->exec('CREATE TABLE IF NOT EXISTS query_cache (id VARCHAR(255), data LONGBLOB, expire DATETIME, PRIMARY KEY(id))');
      } catch (\Exception $e) {
    }
    return Doctrine_Manager::getInstance()->openConnection($pdo, 'cache', false);
  }

  public function configureDoctrineConnection(sfEvent $event) {
    $parameters = $event->getParameters();
    $con = $parameters['connection'];
    /* @var $con Doctrine_Connection */

    $cache = new Doctrine_Cache_Db(array('connection' => $this->cacheCon(), 'tableName' => 'query_cache'));
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $cache);
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, 3600);
  }

  public function clearQueryCache(sfEvent $event) {
    try {
      $this->cachePDO()->exec('TRUNCATE query_cache');
    } catch (\Exception $e) {
    }
  }

  public function initialize() {
    parent::initialize();

    if (!sfConfig::get('sf_cli') && false !== sfConfig::get('app_frontend_csrf_secret')) {
      sfForm::enableCSRFProtection(sfConfig::get('app_frontend_csrf_secret'));
    }
  }

}
