<?php

/**
 * UGLY fix to get Radios with IDs working with ajax form error handling 
 */
class FormatterRadio extends sfWidgetFormSelectRadio {

  private $id_attr;

  public function formatter($widget, $inputs) {
    $rows = array();
    foreach ($inputs as $input) {
      $rows[] = $this->renderContentTag('li', $input['input'] . $this->getOption('label_separator') . $input['label']);
    }

    return !$rows ? '' : $this->renderContentTag('ul', implode($this->getOption('separator'), $rows), array('id' => $this->id_attr, 'class' => $this->getOption('class')));
  }

  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $this->id_attr = $this->generateId($name);
    return parent::render($name, $value, $attributes, $errors);
  }

}