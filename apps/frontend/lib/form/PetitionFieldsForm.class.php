<?php

/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class PetitionFieldsForm extends BasePetitionForm {

  public function configure_fields() {
    // 'nametype', 'with_comments', 'with_address', 'with_extra1', 'with_country', 'default_country', 'country_collection_id'

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
    foreach ($countries_false as $country) {
      if (!is_numeric($country)) {
        $countries[] = $country;
      }
    }
    $countries = array_diff($countries, array('QU', 'ZZ'));
    $this->setWidget('default_country', new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'culture' => 'en', 'add_empty' => ''), array('data-placeholder' => 'No default country')));
    $this->setValidator('default_country', new sfValidatorI18nChoiceCountry(array('countries' => $countries, 'required' => false)));

    $this->setWidget('with_extra1', new sfWidgetFormChoice(array(
        'choices' => array(Petition::WITH_EXTRA_NO => 'no', Petition::WITH_EXTRA_YES => 'yes'),
        'label' => 'Free text field'
      ), array(
        'class' => 'add_popover',
        'data-content' => 'When selected, an extra input field will be added to the sign-up form. You will be asked to set a custom label (title text) for each language of your action.',
    )));
    $this->setValidator('with_extra1', new sfValidatorChoice(array('choices' => array(Petition::WITH_EXTRA_NO, Petition::WITH_EXTRA_YES), 'required' => true)));

    $this->getWidgetSchema()->setLabel('country_collection_id', 'Restrict Countries');
    $this->getWidgetSchema()->setHelp('country_collection_id', 'As a standard, activists can select their home country from a list of all countries in the world. You may restrict the number of country options shown, so activists can pick their country faster.');
    $this->getWidget('country_collection_id')->setAttribute('data-placeholder', 'unrestricted');
  }

}
