<?php

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