<?php

class EditCampaignAddressForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_address[%s]');

    $this->useFields(array('address'));

    $this->setWidget('address', new sfWidgetFormTextarea(
        array('label' => false),
        array('class' => 'span6', 'placeholder' => 'Enter address', 'style' => 'height: 360px'))
    );

    $this->setValidator('address', new sfValidatorString(array(
          'min_length' => 0,
          'max_length' => 500,
          'required' => false
      )));
  }

}