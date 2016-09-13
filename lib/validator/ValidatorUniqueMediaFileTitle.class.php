<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ValidatorUniqueMediaFileTitle extends sfValidatorBase {

  protected function configure($options = array(), $messages = array()) {
    parent::configure($options, $messages);

    $this->addRequiredOption('object');

    $this->setMessage('invalid', 'This title is already used. It must be unique.');
  }

  protected function doClean($value) {
    $table = MediaFileTable::getInstance();
    $object = $this->getOption('object'); /* @var $object MediaFile */

    if (!$value) {
      return $value;
    }

    $query = $matchesOther = $table->createQuery('mf')
      ->where('mf.title = ?', $value)
      ->andWhere('mf.petition_id = ?', $object->getPetitionId());

    if (!$object->isNew()) {
      $query->andWhere('mf.id != ?', $object->getId());
    }

    $matchesOther = $query->count();

    if ($matchesOther) {
      throw new sfValidatorError($this, 'invalid');
    }

    return $value;
  }

}
