<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class CampaignSwitchesForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_switches[%s]');
    
    $this->useFields(array('owner_register'));
    
    $this->setWidget('owner_register', new WidgetBootstrapRadio(array('choices' => Campaign::$OWNER_REGISTER_SHOW, 'label' => 'Widget-owners can apply for transfer of data ownership.')));
    $this->setValidator('owner_register', new sfValidatorChoice(array('choices' => array_keys(Campaign::$OWNER_REGISTER_SHOW))));
    
    $this->setWidget('join_enabled', new WidgetBootstrapRadio(array('choices' => Campaign::$JOIN_ENABLED_SHOW, 'label' => 'Others may ask to join this campaign (User profile/join campaign)')));
    $this->setValidator('join_enabled', new sfValidatorChoice(array('choices' => array_keys(Campaign::$JOIN_ENABLED_SHOW))));
  }

}