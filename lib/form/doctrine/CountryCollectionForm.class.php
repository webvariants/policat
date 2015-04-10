<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * CountryCollection form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
class CountryCollectionForm extends BaseCountryCollectionForm {

  public function configure() {
    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('country[%s]');

    $culture_info = sfCultureInfo::getInstance('en');
    $countries_false = array_keys($culture_info->getCountries());

    $countries_all = array();
    foreach ($countries_false as $country)
      if (!is_numeric($country))
        $countries_all[] = $country;
    $countries = array_diff($countries_all, array('QU', 'ZZ'));

    unset($this['countries']);

    $this->setWidget('countries_list', new sfWidgetFormI18nChoiceCountry(array(
        'multiple' => true,
        'countries' => $countries,
        'culture' => $culture_info->getName(),
        'label' => 'Countries',
        'default' => $this->getObject()->getCountriesList()
    )));
    $this->setValidator('countries_list', new sfValidatorI18nChoiceCountry(array('multiple' => true, 'min' => 1, 'required' => true, 'countries' => $countries)));
  }

}
