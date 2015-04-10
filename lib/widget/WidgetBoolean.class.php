<?php

class WidgetBoolean extends sfWidgetFormInputCheckbox {

  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    return parent::render($name, !empty($value), $attributes, $errors);
  }

}