<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class policatRouting extends sfPatternRouting {

  public function __construct(\sfEventDispatcher $dispatcher, \sfCache $cache = null, $options = array()) {
    parent::__construct($dispatcher, $cache, $options);

    if (!$this->options['context']['host'] && array_key_exists('cli_context', $this->options)) {
      // without host we are on cli

      foreach ($this->options['cli_context'] as $key => $value) {
        $this->options['context'][$key] = $value;
      }
    }
  }

}
