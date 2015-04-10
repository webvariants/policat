<?php

class ValidatorPetitionText extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('petition_id');
    $this->addRequiredOption('petition_text_id');
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    $value = PetitionText::getIdByHash($value);

    if ($value !== false)
    {
      if ($this->getOption('petition_text_id') == $value)
        return $value;

      if (Doctrine_Core::getTable('PetitionText')
      ->createQuery('pt')
      ->where('pt.petition_id = ?', $this->getOption('petition_id'))
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE)
      ->andWhere('pt.id = ?', $value)
      ->limit(1)
      ->count())
        return $value;
    }

    throw new sfValidatorError($this, 'invalid');
  }
}