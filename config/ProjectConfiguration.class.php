<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

require_once __DIR__ . '/../lib/vendor/autoload.php'; // from composer
if (!class_exists('Memcache')) {
  // PHP 7 support for memcache is back! see https://pecl.php.net/package/memcache version 4 🙂
  require_once __DIR__ . '/../lib/memcacheshim/memcacheshim.php';
}
class Doctrine extends Doctrine_Core {}

sfCoreAutoload::register();

// force use of patched sfCultureInfo
require_once __DIR__ . '/../lib/i18n/sfCultureInfo.class.php';

class ProjectConfiguration extends sfProjectConfiguration {

  public function setup() {
    date_default_timezone_set('Etc/UTC');
    $this->enablePlugins(array(
        'sfDoctrinePlugin',
        'sfDoctrineGuardPlugin',
        'sfFormExtraPlugin',
        'sfCacheTaggingPlugin',
        'amgSentryPlugin'
    ));
    sfConfig::set('doctrine_model_builder_options', array('baseClassName' => 'myDoctrineRecord'));

    $this->getEventDispatcher()->connect('doctrine.filter_model_builder_options', function($_, $options) {
      $options['baseClassName'] = 'myDoctrineRecord';
      return $options;
    });
  }

}
