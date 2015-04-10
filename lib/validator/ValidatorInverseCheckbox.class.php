<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorInverseCheckbox extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->options['required'] = false;

    parent::configure($options, $messages);

    $this->addRequiredOption('value_attribute_value');
  }

  protected function doClean($value)
  {
    if ($this->getOption('value_attribute_value') === $value)
      return $this->getOption('empty_value');

    throw new sfValidatorError($this, 'invalid');
  }

  protected function getEmptyValue()
  {
    return $this->getOption('value_attribute_value');
  }
}
