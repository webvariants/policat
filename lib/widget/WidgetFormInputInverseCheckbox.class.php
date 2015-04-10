<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetFormInputInverseCheckbox extends sfWidgetFormInputCheckbox
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return parent::render($name, !$value, $attributes, $errors);
  }
}
