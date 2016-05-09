<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */
class ValidatorUnchanged extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('fix');
    $this->addMessage('changed', 'Something changed meanwhile.');
  }

  protected function doClean($value)
  {
    $errors = array();
    $fix = $this->getOption('fix');
    if ($fix !== $value)
      $errors[] = new sfValidatorError($this, 'changed');

    if (!empty ($errors))
      throw new sfValidatorErrorSchema($this, $errors);

    return null;
  }
}