<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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
