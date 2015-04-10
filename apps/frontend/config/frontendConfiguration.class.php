<?php

class frontendConfiguration extends sfApplicationConfiguration {

  public function configure() {
    $this->getEventDispatcher()->connect('doctrine.configure_connection', array($this, 'configureDoctrineConnection'));
  }

  public function configureDoctrineConnection(sfEvent $event) {
    $parameters = $event->getParameters();
    $con = $parameters['connection'];
    /* @var $con Doctrine_Connection */

    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Array());
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, 3600);
  }

  public function initialize() {
    parent::initialize();

    if (!sfConfig::get('sf_cli') && false !== sfConfig::get('app_frontend_csrf_secret')) {
      sfForm::enableCSRFProtection(sfConfig::get('app_frontend_csrf_secret'));
    }
  }

}
