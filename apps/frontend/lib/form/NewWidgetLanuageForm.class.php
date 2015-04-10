<?php

class NewWidgetLanguageForm extends sfForm {

  const OPTION_PETITION = 'petition';

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('%s');

    $this->setWidget('lang', new sfWidgetFormDoctrineChoice(array(
          'model' => 'PetitionText',
          'query' => PetitionTextTable::getInstance()->queryByPetitionAndActive($this->getOption(self::OPTION_PETITION), true),
          'method' => 'getLanguage',
          'add_empty' => 'select language',
          'label' => false
      )));
  }

}