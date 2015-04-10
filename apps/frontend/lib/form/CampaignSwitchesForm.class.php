<?php

class CampaignSwitchesForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_switches[%s]');
    
    $this->useFields(array('owner_register'));
    
    $this->setWidget('owner_register', new WidgetBootstrapRadio(array('choices' => Campaign::$OWNER_REGISTER_SHOW, 'label' => 'Widget-owners can apply for transfer of data ownership.')));
    $this->setValidator('owner_register', new sfValidatorChoice(array('choices' => array_keys(Campaign::$OWNER_REGISTER_SHOW))));
    
    $this->setWidget('become_petition_admin', new WidgetBootstrapRadio(array('choices' => Campaign::$BECOME_PETITION_ADMIN_SHOW, 'label' => 'Action members can apply to become member-manager of an action.')));
    $this->setValidator('become_petition_admin', new sfValidatorChoice(array('choices' => array_keys(Campaign::$BECOME_PETITION_ADMIN_SHOW))));
  }

}