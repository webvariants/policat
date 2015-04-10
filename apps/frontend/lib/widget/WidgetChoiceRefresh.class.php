<?php

class WidgetChoiceRefresh extends sfWidgetFormChoice {

  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $attributes['data-refresh'] = $value;
    return parent::render($name, $value, $attributes, $errors);
  }

}