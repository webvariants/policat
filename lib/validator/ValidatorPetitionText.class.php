<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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