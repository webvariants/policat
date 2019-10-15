<?php
/*
 * Copyright (c) 2019, webvariants GmbH Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorUniqueEmail extends sfValidatorSchema {

  const OPTION_IS_GEO = 'is_geo';

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->addMessage('old', "Attention: You've already taken part in this action (maybe on another website).");
    $this->addOption(self::OPTION_IS_GEO, false);
    $this->addRequiredOption('petition_id');
  }

  protected function doClean($values) {
    if (!isset($values['email'])) {
      return $values;
    }

    $existing_signing = Doctrine_Core::getTable('PetitionSigning')
      ->createQuery('s')
      ->where('s.petition_id = ?', $this->getOption('petition_id'))
      ->andWhere('s.email = ?', $values['email'])
      ->limit(1)
      ->fetchOne();
    if ($existing_signing && !$this->getOption(self::OPTION_IS_GEO)) {
      /* @var $existing_signing PetitionSigning */

      if ($existing_signing->getStatus() == PetitionSigning::STATUS_PENDING) {
        return $values;
      }

      // allow resigning with same email if not subscribed before und subscribed now
      // see 4eb47883-b48f-43b0-af1f-0726857213cf
      if (isset($values['subscribe'])
        && $values['subscribe']
        && !$existing_signing->getSubscribe()
        && $existing_signing->getStatus() == PetitionSigning::STATUS_COUNTED) {
        return $values;
      }

      $errorSchema = new sfValidatorErrorSchema($this);
      $errorSchema->addError(new sfValidatorError($this, 'old'), 'email');
      throw $errorSchema;
    }

    return $values;
  }

}
