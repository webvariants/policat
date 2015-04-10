<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class WidgetFormInputCheckbox extends sfWidgetFormInput {

  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('value_attribute_value');

    parent::__construct($options, $attributes);
  }

  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setOption('type', 'checkbox');

    if (isset($attributes['value']))
    {
      $this->setOption('value_attribute_value', $attributes['value']);
    }
  }

  public function render($name, $value = null, $attributes = array(), $errors = array()) {

    if (!isset($attributes['value']) && null !== $this->getOption('value_attribute_value'))
    {
      $attributes['value'] = $this->getOption('value_attribute_value');
    }

    return parent::render($name, null, $attributes, $errors);
  }
}
