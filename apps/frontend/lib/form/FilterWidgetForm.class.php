<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class FilterWidgetForm extends policatFilterForm {

  const USER = 'user';
  const WITH_CAMPAIGN = 'with_campaign';

  public function configure() {

    $user = $this->getOption(self::USER);

    if ($this->getOption(self::WITH_CAMPAIGN)) {
      $query = $user ? CampaignTable::getInstance()->queryByMember($user, $this->getOption('is_member', true) ? true : false) : CampaignTable::getInstance()->queryAll();

      $this->setWidget(WidgetTable::FILTER_CAMPAIGN, new sfWidgetFormDoctrineChoice(
        array(
          'model' => 'Campaign',
          'query' => $query,
          'add_empty' => 'select campaign',
          'label' => 'campaign',
        ), array(
          'class' => 'span2 select-update',
          'data-update-target' => '#' . /* $this->getName() . '_' . */ WidgetTable::FILTER_PETITION,
          'data-update-url' => sfContext::getInstance()->getRouting()->generate('action_by_campaign')
      )));

      $this->setValidator(WidgetTable::FILTER_CAMPAIGN, new sfValidatorDoctrineChoice(array(
          'required' => false,
          'model' => 'Campaign',
          'query' => $query
      )));

      $this->setWidget(WidgetTable::FILTER_PETITION, new WidgetChoiceRefresh(
        array(
          'choices' => array('' => 'select action'),
          'label' => 'action',
        ), array('class' => 'span2')
      ));

      $this->setValidator(WidgetTable::FILTER_PETITION, new sfValidatorDoctrineChoice(array(
          'required' => false,
          'model' => 'Petition'
      )));
    }

    $this->setWidget(WidgetTable::FILTER_LANGUAGE, new sfWidgetFormDoctrineChoice(
      array(
        'model' => 'Language',
        'add_empty' => 'select language',
        'label' => 'language'
      ), array('class' => 'span2')
    ));
    $this->setValidator(WidgetTable::FILTER_LANGUAGE, new sfValidatorDoctrineChoice(array(
        'required' => false,
        'model' => 'Language'
    )));

    $status_with_empty = array(0 => 'select status') + Widget::$STATUS_SHOW;
    $this->setWidget(WidgetTable::FILTER_STATUS, new sfWidgetFormChoice(array(
        'choices' => $status_with_empty,
        'label' => 'status'
      ), array('class' => 'span2')
    ));

    $this->setValidator(WidgetTable::FILTER_STATUS, new sfValidatorChoice(array(
        'choices' => array_keys($status_with_empty),
        'required' => false
    )));

    $this->setWidget(WidgetTable::FILTER_START, new sfWidgetFormInput(array(
        'type' => 'date',
        'label' => 'started after'
      ), array(
    )));
    $this->setValidator(WidgetTable::FILTER_START, new sfValidatorDate(array(
        'required' => false
    )));

    $this->setWidget(WidgetTable::FILTER_END, new sfWidgetFormInput(array(
        'type' => 'date',
        'label' => 'ended before'
      ), array(
    )));
    $this->setValidator(WidgetTable::FILTER_END, new sfValidatorDate(array(
        'required' => false
    )));

    $this->setWidget(WidgetTable::FILTER_ORDER, new sfWidgetFormInputHidden());
    $this->setValidator(WidgetTable::FILTER_ORDER, new sfValidatorPass(array('required' => false)));

    $this->setWidget(WidgetTable::FILTER_MIN_SIGNINGS, new sfWidgetFormInput(array(
        'type' => 'number',
        'label' => 'min signings',
        'default' => 1,
      ), array(
        'min' => 0
      )
    ));
    $this->setValidator(WidgetTable::FILTER_MIN_SIGNINGS, new sfValidatorInteger(array(
        'required' => false
    )));
  }

  function filter(Doctrine_Query $query) {
    return WidgetTable::getInstance()->filter($query, $this);
  }

}