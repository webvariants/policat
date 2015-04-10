<?php
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