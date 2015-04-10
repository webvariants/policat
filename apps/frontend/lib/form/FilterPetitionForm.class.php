<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class FilterPetitionForm extends policatFilterForm implements policatFilter {

  const USER = 'user';
  const WITH_CAMPAIGN = 'with_campaign';
  const DELETED_TOO = 'deleted_too';

  public function configure() {

    $user = $this->getOption(self::USER);

    if ($this->getOption(self::WITH_CAMPAIGN)) {
      $query = $user ? CampaignTable::getInstance()->queryByMember($user, $this->getOption('is_member', true) ? true : false) : CampaignTable::getInstance()->queryAll();

      $this->setWidget(PetitionTable::FILTER_CAMPAIGN, new sfWidgetFormDoctrineChoice(
        array(
          'model' => 'Campaign',
          'query' => $query,
          'add_empty' => 'select campaign',
          'label' => 'campaign',
        ), array('class' => 'span2')
      ));

      $this->setValidator(PetitionTable::FILTER_CAMPAIGN, new sfValidatorDoctrineChoice(array(
          'required' => false,
          'model' => 'Campaign',
          'query' => $query
      )));
    }

    $status_with_empty = array(0 => 'select status') + Petition::$STATUS_SHOW;
    if (!$this->getOption(self::DELETED_TOO))
      unset($status_with_empty[Petition::STATUS_DELETED]);
    $this->setWidget(PetitionTable::FILTER_STATUS, new sfWidgetFormChoice(array(
        'choices' => $status_with_empty,
        'label' => 'status'
      ), array('class' => 'span2')
    ));

    $this->setValidator(PetitionTable::FILTER_STATUS, new sfValidatorChoice(array(
        'choices' => array_keys($status_with_empty),
        'required' => false
    )));

    $kinds_with_empty = array(0 => 'select type') + Petition::$KIND_SHOW;
    $this->setWidget(PetitionTable::FILTER_KIND, new sfWidgetFormChoice(array(
        'choices' => $kinds_with_empty,
        'label' => 'type'
      ), array('class' => 'span2')
    ));

    $this->setValidator(PetitionTable::FILTER_KIND, new sfValidatorChoice(array(
        'choices' => array_keys($kinds_with_empty),
        'required' => false
    )));

    $this->setWidget(PetitionTable::FILTER_START, new sfWidgetFormInput(array(
        'type' => 'date',
        'label' => 'started after'
      ), array(
    )));
    $this->setValidator(PetitionTable::FILTER_START, new sfValidatorDate(array(
        'required' => false
    )));

    $this->setWidget(PetitionTable::FILTER_END, new sfWidgetFormInput(array(
        'type' => 'date',
        'label' => 'ended before'
      ), array(
    )));
    $this->setValidator(PetitionTable::FILTER_END, new sfValidatorDate(array(
        'required' => false
    )));

    $this->setWidget(PetitionTable::FILTER_ORDER, new sfWidgetFormInputHidden());
    $this->setValidator(PetitionTable::FILTER_ORDER, new sfValidatorPass(array('required' => false)));

    $this->setWidget(PetitionTable::FILTER_MIN_SIGNINGS, new sfWidgetFormInput(array(
        'type' => 'number',
        'label' => 'min signings',
        'default' => 1,
      ), array(
        'min' => 0
      )
    ));
    $this->setValidator(PetitionTable::FILTER_MIN_SIGNINGS, new sfValidatorInteger(array(
        'required' => false
    )));
  }

  function filter(Doctrine_Query $query) {
    return PetitionTable::getInstance()->filter($query, $this);
  }

}
