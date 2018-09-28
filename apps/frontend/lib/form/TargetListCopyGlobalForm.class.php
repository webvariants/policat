<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class TargetListCopyGlobalForm extends sfForm {

  public function setup() {
    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('target_global[%s]');

    $query = MailingListTable::getInstance()->queryGlobalActive();

    $this->setWidget('global', new sfWidgetFormDoctrineChoice(
        array(
            'model' => 'MailingList',
            'query' => $query,
            'add_empty' => '-- select Target-list --',
            'label' => 'Source'
        ),
        array(
            'class' => 'span4',
            'onchange' => "$('#target_global_new_name').val($('option:selected', this).text());"
      ))
    );

    $this->setValidator('global', new sfValidatorDoctrineChoice(array(
          'required' => true,
          'model' => 'MailingList',
          'query' => $query
      )));

    $this->setWidget('new_name', new sfWidgetFormInputText(array(), array('class' => 'span4')));
    $this->setValidator('new_name', new sfValidatorString(array('max_length' => 100, 'required' => true)));
  }

}