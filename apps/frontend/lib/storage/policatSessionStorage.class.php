<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatSessionStorage extends sfSessionStorage {

  public function initialize($options = null) {
    $auto_start = !is_array($options) || !array_key_exists('auto_start', $options) || $options['auto_start'] != false;
    $session_name = is_array($options) && array_key_exists('session_name', $options) ? $options['session_name'] : 'symfony';
    $delay_auto_start = false;

    if ($auto_start && empty($_COOKIE[$session_name])) {
      if (!is_array($options)) {
        $options = array();
      }

      $options['auto_start'] = false;
      $delay_auto_start = true;
    }

    parent::initialize($options);

    if ($delay_auto_start) {
      $this->options['auto_start'] = true;
    }
  }

  public function needSession() {
    if ($this->options['auto_start']) {
      if (!session_id() && !self::$sessionStarted) {
        session_start();
        self::$sessionStarted = true;
      }
    }
  }

  public function dropSession() {
    $this->shutdown();
    self::$sessionStarted = false;
    $session_name = $this->options['session_name'];
    if (!empty($_COOKIE[$session_name])) {
      unset($_COOKIE[$session_name]);
      setcookie($session_name, '', time() - 3600);
    }
  }

}
