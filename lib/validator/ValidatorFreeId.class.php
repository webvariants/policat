<?php

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
