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
 * Owner form.
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class OwnerForm extends BaseOwnerForm
{
  public function configure()
  {
    unset(
      $this['status'],
      $this['campaign_id'],
      $this['first_widget_id'],
      $this['email']
    );

    $this->widgetSchema->setFormFormatterName('policat');

    $countries_false = array_keys(sfCultureInfo::getInstance()->getCountries());
    $countries = array();
    foreach ($countries_false as $country) if (!is_numeric($country)) $countries[] = $country;
    $countries = array_diff ($countries, array('QU', 'ZZ', 'MF'));

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry(array('countries' => $countries, 'add_empty' => 'Country')));
    $this->setValidator('country', new sfValidatorI18nChoiceCountry(array('countries' => $countries, 'required' => true)));

    //$this->setValidator('email', new ValidatorEmail());

    $this->setWidget('password', new sfWidgetFormInputPassword(array(), array('autocomplete' => 'off')));
    $this->setValidator('password', new sfValidatorString(array('min_length' => 20, 'max_length' => 100, 'required' => true)));
    $this->setWidget('password_again', new sfWidgetFormInputPassword(array(), array('autocomplete' => 'off')));
    $this->setValidator('password_again', new sfValidatorString());
    $this->getWidgetSchema()->moveField('password_again', sfWidgetFormSchema::AFTER, 'password');
    $this->getValidatorSchema()->setPostValidator(new sfValidatorSchemaCompare('password', '===', 'password_again', array(), array(
        'invalid' => 'passwords do not match'
    )));
    $this->getWidgetSchema()->setHelp('password', 'at least 20 characters');

    foreach (array('firstname', 'lastname', 'function', 'organisation', 'phone', 'address') as $field)
      $this->getValidator($field)->setOption('required', true);
  }

  public function updatePasswordColumn($hash) {
    $salt = sha1(mt_rand() . time() . '_42');
    return $salt . '_' . sha1($salt . $hash);
  }
}
