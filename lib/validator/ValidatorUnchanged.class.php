<?php
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