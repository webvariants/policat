<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorUniqueEmail extends ValidatorEmail {

  const OPTION_IS_GEO = 'is_geo';
  const OPTION_IGNORE_PENDING = 'ignore_pending';

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->addMessage('old', "Attention: You've already taken part in this action (maybe on another website).");
    $this->addOption(self::OPTION_IS_GEO, false);
    $this->addRequiredOption('petition_id');
    $this->addOption(self::OPTION_IGNORE_PENDING, false);
  }

  protected function doClean($value) {
    $clean = parent::doClean($value);
    $existing_signing = Doctrine_Core::getTable('PetitionSigning')
      ->createQuery('s')
      ->where('s.petition_id = ?', $this->getOption('petition_id'))
      ->andWhere('s.email = ?', $clean)
      ->limit(1)
      ->fetchOne();
    if ($existing_signing && !$this->getOption(self::OPTION_IS_GEO)) {
      /* @var $existing_signing PetitionSigning */

      if ($this->getOption(self::OPTION_IGNORE_PENDING) && ($existing_signing->getStatus() == PetitionSigning::STATUS_PENDING)) {

        return $clean;
      }

      throw new sfValidatorError($this, 'old');
      ;
    }

    return $clean;
  }

}
