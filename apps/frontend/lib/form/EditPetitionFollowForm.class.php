<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class EditPetitionFollowForm extends BasePetitionForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('edit_petition_follow[%s]');

    $this->useFields(array());

    $choice_ids = array();
    $choices = array('' => '-No forwarding-');
    foreach (PetitionTable::getInstance()->fetchNoCycleChoices($this->getObject()) as $choice) {
      /* @var $choice Petition */
      $choice_ids[] = $choice->getId();
      $choices[$choice->getId()] = $choice->getName();
    }
    
    $this->setWidget('follow_petition_id', new sfWidgetFormChoice(array(
        'choices' => $choices,
        'label' => false
    )));

    $this->setValidator('follow_petition_id', new sfValidatorChoice(array(
        'choices' => $choice_ids,
        'required' => false
    )));
  }

}
