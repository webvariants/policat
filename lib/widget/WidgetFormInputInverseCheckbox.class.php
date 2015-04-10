<?php

class WidgetFormInputInverseCheckbox extends sfWidgetFormInputCheckbox
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return parent::render($name, !$value, $attributes, $errors);
  }
}