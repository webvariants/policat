<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class LanguageTable extends Doctrine_Table {

  /**
   *
   * @return LanguageTable
   */
  public static function getInstance() {
    return Doctrine_Core::getTable('Language');
  }

  /**
   *
   * @return Doctrine_Query
   */
  public function queryAll() {
    return $this->createQuery('l')->orderBy('l.order_number');
  }

  public function fetchLanguageIds() {
    return $this->queryAll()->select('l.id')->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  public function queryByActivePetitionTexts(Petition $petition) {
    return $this->queryAll()
      ->leftJoin('l.PetitionText pt')
      ->andWhere('pt.petition_id = ?', $petition->getId())
      ->andWhere('pt.status = ?', PetitionText::STATUS_ACTIVE);
  }

}
