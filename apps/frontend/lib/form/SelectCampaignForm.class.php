<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class SelectCampaignForm extends sfForm {
  const NAME = 'name';
  const HELP = 'help';
  const IS_MEMBER = 'is_member';
  const USER = 'user';
  const EMPTY_STR = 'empty';
  const JOINABLE = 'joinable';

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat($this->getOption(self::NAME, 'select_campaign') . '[%s]');

    $user = $this->getOption(self::USER);
    $query = $user ? CampaignTable::getInstance()->queryByMember($user, $this->getOption(self::IS_MEMBER, true) ? true : false, false, false) : CampaignTable::getInstance()->queryAll();
    /* @var $query Doctrine_Query */
    $query->orderBy($query->getRootAlias() . '.name ASC');
    
    if ($this->getOption(self::JOINABLE, false)) {
      $query->andWhere($query->getRootAlias() . '.join_enabled = ?', Campaign::JOIN_ENABLED_YES);
    }
    
    $empty = $this->getOption(self::EMPTY_STR);
    if (!$empty) {
      $empty = 'select campaign';
    }

    $this->setWidget('id', new sfWidgetFormDoctrineChoice(
        array(
            'model' => 'Campaign',
            'query' => $query,
            'add_empty' => $empty,
            'label' => false
        ),
        array('class' => 'input-medium'))
    );

    $this->setValidator('id', new sfValidatorDoctrineChoice(array(
          'required' => true,
          'model' => 'Campaign',
          'query' => $query
      )));

    $help = $this->getOption(self::HELP, '');
    if ($help) {
      $this->getWidget('id')->setAttribute('class', 'input-medium add_popover');
      $this->getWidget('id')->setAttribute('data-content', $help);
    }
  }

}