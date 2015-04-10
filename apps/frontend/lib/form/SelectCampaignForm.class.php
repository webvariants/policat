<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class SelectCampaignForm extends sfForm {
  const NAME = 'name';
  const HELP = 'help';

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat($this->getOption(self::NAME, 'select_campaign') . '[%s]');

    $user = $this->getOption('user');
    $query = $user ? CampaignTable::getInstance()->queryByMember($user, $this->getOption('is_member', true) ? true : false) : CampaignTable::getInstance()->queryAll();
    /* @var $query Doctrine_Query */
    $query->orderBy($query->getRootAlias() . '.name ASC');

    $empty = $this->getOption('empty');
    if (!$empty) $empty = 'select campaign';

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
      $this->getWidget('id')->setAttribute('class', 'input-medium add_popover popover_left');
      $this->getWidget('id')->setAttribute('data-content', $help);
    }
  }

}
