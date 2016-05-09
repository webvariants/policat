<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorSchemaRequireOne extends sfValidatorSchema {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->addOption('number', 1);
    $this->addRequiredOption('fields');
    $this->addMessage('too_many', 'You have filled too many fields.');
    $this->addMessage('too_less', 'You must fill one of the fields');
  }

  protected function doClean($values) {
    $fields = $this->getOption('fields');
    $number = 0;
    foreach ($fields as $field) {
      if (array_key_exists($field, $values) && $values[$field]) {
        $number++;
      }
    }

    $errorSchema = new sfValidatorErrorSchema($this);

    if ($number > $this->getOption('number')) {
      $errorSchema->addError(new sfValidatorError($this, $this->getMessage('too_many')), $field);
    }

    if ($number < $this->getOption('number')) {
      $errorSchema->addError(new sfValidatorError($this, $this->getMessage('too_less')), $field);
    }

    if ($errorSchema->count())
      throw $errorSchema;

    return $values;
  }

}
