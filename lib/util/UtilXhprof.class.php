<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UtilXhprof {

  private static $running = false;

  public static function start() {
    if (self::$running) {
      return;
    }
    self::$running = true;
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_NO_BUILTINS);

    register_shutdown_function(function() {
      if (!self::$running) {
        return;
      }
      self::$running = false;
      $xhprof_data = xhprof_disable();
      if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
      }
      UtilXhprof::getRuns()->save_run($xhprof_data, UtilXhprof::getSource());
    });
  }
  
  public static function getSource() {
    $routing = sfContext::getInstance()->getRouting();
    if ($routing) {
      return strtr($routing->getCurrentInternalUri(true), array('/' => '_', '@' => '', '?' => '_', '=' => '_'));
    } else {
      return 'xhprof';
    }
  }

  public static function stop() {
    if (!self::$running) {
      return;
    }
    self::$running = false;
    $xhprof_data = xhprof_disable();
    self::getRuns()->save_run($xhprof_data, UtilXhprof::getSource());
  }

  public static function getXhprofRoot() {
    return __DIR__ . "/../vendor/lox/xhprof";
  }

  public static function getRuns() {
    $XHPROF_ROOT = self::getXhprofRoot();
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

    $dir = sfConfig::get('sf_log_dir') . '/xhprof';
    @mkdir($dir);
    return new XHProfRuns_Default($dir);
  }

}
