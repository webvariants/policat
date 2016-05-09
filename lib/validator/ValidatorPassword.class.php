<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorPassword extends sfValidatorString {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);
    
    $this->addMessage('max_length', 'Password is too long (%max_length% characters max).');
    $this->addMessage('min_length', 'Password is too short (%min_length% characters min).');
    
    $this->addMessage('number', 'Password requires at least one number.');
    $this->addMessage('upper', 'Password requires at least one capital letter.');
    $this->addMessage('lower', 'Password requires at least one letter.');
  }
  
  protected function doClean($value) {
    $value = parent::doClean($value);

    if (!preg_match('/[0-9]/', $value))
      throw new sfValidatorError($this, 'number');
    if (!preg_match('/[a-z]/', $value))
      throw new sfValidatorError($this, 'lower');
    if (!preg_match('/[A-Z]/', $value))
      throw new sfValidatorError($this, 'upper');

    return $value;
  }

}