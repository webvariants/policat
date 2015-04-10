<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class NewPetitionForm extends BasePetitionForm {

  const OPTION_USER = 'user';

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('new_petition[%s]');

    $this->useFields(array('name', 'campaign_id', 'kind', 'nametype', 'with_comments', 'with_address', 'with_country', 'default_country', 'country_collection_id'));

    $this->getWidget('campaign_id')->setOption('query', CampaignTable::getInstance()->queryByMember($this->getOption(self::OPTION_USER)));
    $this->getValidator('campaign_id')->setOption('query', CampaignTable::getInstance()->queryByMember($this->getOption(self::OPTION_USER)));
    $this->getWidget('campaign_id')->setAttribute('class', 'add_popover');
    $this->getWidget('campaign_id')->setAttribute('data-content', 'Assign your action to one of your campaigns. Note that your action must comply with the privacy policy of the campaign selected.');

    $this->setWidget('kind', new sfWidgetFormChoice(array('choices' => Petition::$KIND_SHOW_CREATE), array(
        'class' => 'add_popover',
        'data-content' => 'Select the type of your action carefully. You won\'t be able to change the action type once you\'ve created your new e-action.',
    )));
    $this->setValidator('kind', new sfValidatorChoice(array('choices' => array_keys(Petition::$KIND_SHOW_CREATE), 'required' => true)));
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

//    $this->mergePostValidator(new ValidatorSchemaPetitionConf());

    $this->getWidgetSchema()->setLabel('country_collection_id', 'Restrict Countries');
    $this->getWidgetSchema()->setHelp('country_collection_id', 'As a standard, activists can select their home country from a list of all countries in the world. You may restrict the number of country options shown, so activists can pick their country faster.');
  }

}
