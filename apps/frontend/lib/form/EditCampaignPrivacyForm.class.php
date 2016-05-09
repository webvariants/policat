<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class EditCampaignPrivacyForm extends BaseCampaignForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrapInline');
    $this->widgetSchema->setNameFormat('campaign_privacy[%s]');

    $this->useFields(array('privacy_policy'));

    $this->setWidget('privacy_policy', new sfWidgetFormTextarea(
        array('label' => false),
        array('class' => 'span6', 'placeholder' => 'Enter privacy agreement', 'style' => 'height: 360px'))
    );

    $this->setValidator('privacy_policy', new sfValidatorString(array(
          'min_length' => 30,
          'max_length' => 60000,
          'required' => true
      )));
  }

}