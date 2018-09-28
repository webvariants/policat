<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class ProfileForm extends BasesfGuardRegisterForm {

  public function setup() {
    parent::setup();

    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('profile[%s]');

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry);
    $this->setValidator('country', new sfValidatorI18nChoiceCountry);

    $this->setValidator('email_address', new ValidatorEmail(array('max_length' => 80)));

    foreach (array('email_address', 'password', 'password_again', 'first_name', 'last_name', 'organisation', 'website', 'street',
      'post_code', 'city', 'country', 'mobile', 'phone', 'language_id') as $field)
      $this->getValidator($field)->setOption('required', true);

    $this->useFields(array(
        'email_address', 'password', 'password_again', 'first_name', 'last_name', 'organisation', 'website', 'street',
        'post_code', 'city', 'country', 'mobile', 'phone', 'language_id', 'vat'
      ), true);

    $this->setValidator('password', new ValidatorPassword(array(
          'required' => false,
          'min_length' => 10,
          'max_length' => 100
      )));

    $this->getValidator('password_again')->setOption('required', false);

    $this->getWidgetSchema()->setHelp('password', 'Your password must be at least 10 characters long, and include at least one number and one capital letter.');
    $this->getWidgetSchema()->setHelp('street', 'In accordance with our terms of service and legal obligations, you must provide your, or your organisations\' legal address.');
    
    $this->getWidgetSchema()->setLabel('vat', 'VAT no. (if applicable)');
    $this->getWidgetSchema()->setHelp('vat', 'Leave this field empty if you\'re an individual, group or NGO without a VAT identification number (VATIN). If you add your VATIN and are based in an EU member state except Germany, you will not be charged VAT. Make sure to include the full number preceded by the country code. Check the validity of your VATIN here: http://ec.europa.eu/taxation_customs/vies/');

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
          new sfValidatorDoctrineUnique(
            array('model' => 'sfGuardUser', 'column' => array('email_address')),
            array('invalid' => 'An user account with this e-mail exists already.')
          ),
          new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username'))),
          new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.'))
      ))
    );
    
    $this->mergePostValidator(new ValidatorVat(null, array('country' => 'country', 'vat' => 'vat')));
  }

}