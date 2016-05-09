<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetChoiceRefresh extends sfWidgetFormChoice {

  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $attributes['data-refresh'] = $value;
    return parent::render($name, $value, $attributes, $errors);
  }

}