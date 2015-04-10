<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorFreeId extends sfValidatorBase
{
  const OPTION_MODEL = 'model';

  public function __construct($options = array(), $messages = array()) {
    $this->addRequiredOption(self::OPTION_MODEL);
    parent::__construct($options, $messages);
    $this->addMessage('used', "Sorry ID already used");

  }

  protected function doClean($value) {
    if (empty($value)) return null;
    if (is_numeric($value)) {
      $id = (int) $value;
      $model = $this->getOption(self::OPTION_MODEL);

      if (Doctrine_Core::getTable($model)->createQuery('a')->where('a.id = ?', $id)->count())
        throw new sfValidatorError($this, 'used');
      else
        return $id;
    }

    throw new sfValidatorError($this, 'invalid');
  }
}
