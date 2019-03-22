<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class NewWidgetLanguageForm extends sfForm {

  const OPTION_PETITION = 'petition';

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('%s');

    $this->setWidget('lang', new sfWidgetFormDoctrineChoice(array(
          'model' => 'PetitionText',
          'query' => PetitionTextTable::getInstance()->queryByPetitionAndActive($this->getOption(self::OPTION_PETITION), true),
          'method' => 'getLanguage',
          'add_empty' => 'Select language',
          'label' => false
      ), array('class' => 'no-chosen form-control')));
  }

}