<?php

class widgetConfiguration extends sfApplicationConfiguration {

  public function configure() {
    $this->getEventDispatcher()->connect('doctrine.configure_connection', array($this, 'configureDoctrineConnection'));
  }

  public function configureDoctrineConnection(sfEvent $event) {
    $parameters = $event->getParameters();
    $con = $parameters['connection'];
    /* @var $con Doctrine_Connection */

    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, new Doctrine_Cache_Proxy(array('prefix' => 'WI_DQ_')));
    $con->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, 3600);
  }

}
