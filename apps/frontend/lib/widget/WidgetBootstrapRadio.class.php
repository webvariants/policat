<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetBootstrapRadio extends sfWidgetFormChoice {

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    if (!isset($options['renderer_class']))
      $this->setOption('renderer_class', 'WidgetFormSelectBootstrapRadio');
  }

}
