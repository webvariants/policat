<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */
class MemberRightsForm extends MemberForm
{
  function configure()
  {
    parent::configure();
    
    $this->useFields(array('group_list'));
    unset ($this['id']);
    $this->setWidget('group_list', new WidgetFormDoctrineSelectDoubleList(array('model' => 'Group')));
    $this->widgetSchema['group_list']->setLabel($this->getObject()->getSfGuardUser()->getUsername());

    $this->getWidgetSchema()->setNameFormat('MemberRights[%s]');
  }
}