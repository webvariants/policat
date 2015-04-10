<?php

require_once __DIR__ . '/../lib/vendor/autoload.php'; // from composer
class Doctrine extends Doctrine_Core {}

sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration {

  public function setup() {
    date_default_timezone_set('Etc/UTC');
    $this->enablePlugins('sfDoctrinePlugin');
    sfConfig::set('doctrine_model_builder_options', array('baseClassName' => 'myDoctrineRecord'));
    $this->enablePlugins('sfDoctrineGuardPlugin');
    $this->enablePlugins('sfFormExtraPlugin');
    $this->enablePlugins('sfCacheTaggingPlugin');

    $this->getEventDispatcher()->connect('doctrine.filter_model_builder_options', function($_, $options) {
      $options['baseClassName'] = 'myDoctrineRecord';
      return $options;
    });
  }

}
