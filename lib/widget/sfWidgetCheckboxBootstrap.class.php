<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sfWidgetCheckboxBootstrap extends sfWidgetFormInputCheckbox {

  public function __construct($options = array(), $attributes = array()) {
    $this->addOption('inner_label');
    $this->addOption('inner_label_escape', true);

    parent::__construct($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    if ($this->getOption('inner_label')) {
      $inner_label = $this->getOption('inner_label_escape') ?  $this->escapeOnce($this->getOption('inner_label')) : $this->getOption('inner_label');
      return '<label class="checkbox">' . parent::render($name, $value, $attributes, $errors) . $inner_label . '<label>';
    }
    return parent::render($name, $value, $attributes, $errors);
  }

}