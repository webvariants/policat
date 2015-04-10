<?php

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
