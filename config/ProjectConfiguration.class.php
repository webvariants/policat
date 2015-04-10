<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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
