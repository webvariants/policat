<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class NewPetitionForm extends PetitionFieldsForm {

  const OPTION_USER = 'user';

  private function canCreateCamapaign() {
    return $this->getOption(self::OPTION_USER)->hasPermission(myUser::CREDENTIAL_ADMIN) || StoreTable::value(StoreTable::CAMAPIGN_CREATE_ON);
  }

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('new_petition[%s]');

    $this->useFields(array('name', 'campaign_id', 'kind', 'nametype', 'with_comments', 'with_address', 'with_country', 'default_country', 'country_collection_id'));

    $this->getWidget('campaign_id')->setOption('query', CampaignTable::getInstance()->queryByMember($this->getOption(self::OPTION_USER)));
    $this->getWidget('campaign_id')->setOption('add_empty', '');
    $this->getValidator('campaign_id')->setOption('query', CampaignTable::getInstance()->queryByMember($this->getOption(self::OPTION_USER)));
    $this->getWidgetSchema()->setLabel('campaign_id', 'in Campaign');
    $this->getWidget('campaign_id')->setAttribute('class', 'add_popover');
    $this->getWidget('campaign_id')->setAttribute('data-content', 'Assign your action to one of your campaigns. Note that your action must comply with the privacy policy of the campaign selected.');


    if ($this->canCreateCamapaign()) {
      $this->getValidator('campaign_id')->setOption('required', false);
      $this->setWidget('new_campaign', new sfWidgetFormInputText(array(
          'label' => 'or create new campaign'
        ), array(
          'class' => 'add_popover',
          'data-content' => 'You can run as many actions as you want within a campaign and so build your list of supporters over time. Note that you need to buy a separate package for each campaign.'
      )));
      $this->setValidator('new_campaign', new sfValidatorString(array(
          'required' => false,
          'max_length' => 80
      )));
    }

    $this->setWidget('kind', new sfWidgetFormChoice(array('choices' => Petition::$KIND_SHOW), array(
        'class' => 'add_popover',
        'data-content' => 'Select the type of your action carefully. You won\'t be able to change the action type once you\'ve created your new e-action.',
    )));
    $this->setValidator('kind', new sfValidatorChoice(array('choices' => array_keys(Petition::$KIND_SHOW), 'required' => true)));
    $this->getWidgetSchema()->setLabel('kind', 'E-action type');

    $this->setWidget('name', new sfWidgetFormInput(array(
        'label' => 'Action name'
      ), array(
        'class' => 'add_popover',
        'size' => 90,
        'data-content' => 'Give your action a short and memorisable name. It won\'t be shown to your supporters. It\'s only for your and your colleague\'s overview.',
        'rel' => 'popover'
    )));

    $this->configure_fields();

    if ($this->canCreateCamapaign()) {
      $this->mergePostValidator(new ValidatorSchemaRequireOne(array(), array('fields' => array('campaign_id', 'new_campaign')), array(
          'too_many' => 'You can not select both. Select campaign or create one.',
          'too_less' => 'Select an existing campaign or crate a new one.'
      )));
    }
  }

  public function processValues($values) {
    if ($this->canCreateCamapaign()) {
      if ($values['new_campaign']) {
        $campaign = new Campaign();
        $campaign->setName($values['new_campaign']);
        $campaign->setDataOwner($this->getOption(self::OPTION_USER));
        $campaign->setBillingEnabled(StoreTable::value(StoreTable::BILLING_ENABLE) ? 1 : 0);
        $campaign->setOwnerRegister(StoreTable::value(StoreTable::CAMAPIGN_REGISTER_OWNER) ? 1 : 0);
        $store = StoreTable::getInstance()->findByKey(StoreTable::PRIVACY_AGREEMENT);
        if ($store) {
          $campaign->setPrivacyPolicy($store->getField('text'));
        }
        $campaign->save();

        $cr = new CampaignRights();
        $cr->setCampaign($campaign);
        $cr->setUser($this->getOption(self::OPTION_USER));
        $cr->setActive(1);
        $cr->setAdmin(1);
        $cr->setMember(1);
        $cr->save();

        $values['campaign_id'] = $campaign->getId();
      }
      unset($values['new_campaign']);
    }

    if ($values['kind'] == Petition::KIND_PETITION) {
      $values['validation_required'] = Petition::VALIDATION_REQUIRED_NO;
    }

    return parent::processValues($values);
  }

}
