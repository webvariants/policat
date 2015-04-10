<?php

class sfWidgetFormSelectPetitionTextHash extends sfWidgetFormSelect
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($value && is_string($value)) $value = PetitionText::getHashForId($value);
    return parent::render($name, $value, $attributes, $errors);
  }
}