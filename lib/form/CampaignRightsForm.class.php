<?php
class CampaignRightsForm extends CampaignForm
{
  function configure()
  {
    parent::configure();

    $this->useFields(array('id'));
    $this->embedRelation('Member', 'MemberRightsForm');
    $this->addSaveManyToMany(array('Member'), array('group_list' => 'Group'));

    foreach ($this['Member'] as $key => $value) $this->widgetSchema['Member'][$key]->setLabel(false);
    $this->widgetSchema['Member']->setLabel('Member rights');
  }
}