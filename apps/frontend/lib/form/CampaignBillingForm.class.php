<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class CampaignBillingForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_billing[%s]');
    
    $this->useFields(array('billing_enabled'));
    
    $this->setWidget('billing_enabled', new WidgetBootstrapRadio(array('choices' => Campaign::$BILLING_ENABLED_SHOW, 'label' => 'Billing enabled for this campaign.')));
    $this->setValidator('billing_enabled', new sfValidatorChoice(array('choices' => array_keys(Campaign::$BILLING_ENABLED_SHOW))));
  }

}
