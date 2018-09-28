<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class TargetListCopyForm extends sfForm {

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('target_copy[%s]');

    $user = $this->getOption('user');
    $query = $user ? CampaignTable::getInstance()->queryByMember($user) : CampaignTable::getInstance()->queryAll();

    $this->setWidget('target', new sfWidgetFormDoctrineChoice(
        array(
            'model' => 'Campaign',
            'query' => $query,
            'add_empty' => $user ? 'select target campaign' : '-- copy to global pool --',
            'label' => 'Target'
        ),
        array('class' => 'span4'))
    );

    $this->setValidator('target', new sfValidatorDoctrineChoice(array(
          'required' => $user ? true : false,
          'model' => 'Campaign',
          'query' => $query
      )));
    
    $this->setWidget('new_name', new sfWidgetFormInputText(array(), array('class' => 'span4')));
    $this->setValidator('new_name', new sfValidatorString(array('max_length' => 100, 'required' => true)));
  }

}