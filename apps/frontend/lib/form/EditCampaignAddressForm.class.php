<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

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