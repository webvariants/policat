<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class NewPetitionForm extends BasePetitionForm {

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

    $this->setWidget('name', new sfWidgetFormTextarea(array(
        'label' => 'Action name'
      ), array(
        'class' => 'add_popover',
        'cols' => 90,
        'rows' => 2,
        'data-content' => 'Give your action a short and memorisable name. It won\'t be shown to your supporters. It\'s only for your and your colleague\'s overview.',
        'rel' => 'popover'
    )));

    $this->setWidget('nametype', new sfWidgetFormChoice(array(
        'choices' => Petition::$NAMETYPE_SHOW,
        'label' => 'Name'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'Select the type of data you would like to collect you\'re your activists. We recommend asking your supporters to only provide their full name in one field: this will increase the count of your supporters and respects the principle of data economy.'
    )));
    $this->setValidator('nametype', new sfValidatorChoice(array('choices' => array_keys(Petition::$NAMETYPE_SHOW), 'required' => true)));

    $this->setWidget('with_address', new sfWidgetFormChoice(array(
        'choices' => Petition::$WITH_ADDRESS_SHOW,
        'label' => 'Postal address'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'We recommend that you only ask your activists for their personal data if you really need that for your campaign. The more data you ask, the less people will complete the sign-up form.'
    )));
    $this->setValidator('with_address', new sfValidatorChoice(array('choices' => array_keys(Petition::$WITH_ADDRESS_SHOW), 'required' => true)));

    $this->setWidget('with_country', new sfWidgetFormChoice(array(
        'choices' => array(1 => 'Country selector', 0 => 'Don\'t ask'),
        'label' => 'Country'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'We recommend that you always use the country selector if your campaign is international. This allows you to segment the data and have a clear overview how many people signed on from what country. If your action is strictly national, don\'t ask.'
    )));
    $this->setValidator('with_country', new sfValidatorChoice(array('choices' => array(0, 1), 'required' => true)));

    $this->setWidget('with_comments', new sfWidgetFormChoice(array(
        'choices' => array(0 => 'Don\'t ask', 1 => 'Comment box'),
        'label' => 'Comment'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'We recommend that you only ask your activists to provide a comment if you intend to use their comments in your campaign. The comment box will appear underneath the sign-up form.'
    )));
    $this->setValidator('with_comments', new sfValidatorChoice(array('choices' => array(0, 1), 'required' => true)));

    $culture_info = sfCultureInfo::getInstance('en');
    $countries_false = array_keys($culture_info->getCountries());
    $countries = array();
    foreach ($countries_false as $country)
      if (!is_numeric($country))
        $countries[] = $country;
    $countries = array_diff($countries, array('QU', 'ZZ'));
    $this->setWidget('default_country', new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'culture' => 'en', 'add_empty' => 'Country')));
    $this->setValidator('default_country', new sfValidatorI18nChoiceCountry(array('countries' => $countries, 'required' => false)));
    
    $this->setWidget('with_extra1', new sfWidgetFormChoice(array(
        'choices' => array(Petition::WITH_EXTRA_NO => 'no', Petition::WITH_EXTRA_YES => 'yes'),
        'label' => 'Free text field'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'When selected, an extra input field will be added to the sign-up form. You will be asked to set a custom label (title text) for each language of your action.',
    )));
    $this->setValidator('with_extra1', new sfValidatorChoice(array('choices' => array(Petition::WITH_EXTRA_NO, Petition::WITH_EXTRA_YES), 'required' => true)));

//    $this->mergePostValidator(new ValidatorSchemaPetitionConf());

    $this->getWidgetSchema()->setLabel('country_collection_id', 'Restrict Countries');
    $this->getWidgetSchema()->setHelp('country_collection_id', 'As a standard, activists can select their home country from a list of all countries in the world. You may restrict the number of country options shown, so activists can pick their country faster.');
    
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
