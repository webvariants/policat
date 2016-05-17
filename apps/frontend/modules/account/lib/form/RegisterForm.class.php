<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class RegisterForm extends BasesfGuardRegisterForm {

  public function setup() {
    parent::setup();

    $this->disableLocalCSRFProtection();

    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('register[%s]');

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

    $routing = sfContext::getInstance()->getRouting();
    $terms = $routing->generate('terms');

    $this->setWidget('terms', new sfWidgetCheckboxBootstrap(array(
          'label' => false,
          'inner_label' => 'I have read and accepted the <a target="_blank" href="'. $terms .'">terms of service</a>. I will handle any activist data in accordance with the privacy policy, as defined in my campaigns and actions.',
          'inner_label_escape' => false,
          'value_attribute_value' => 'yes'
      )));

    $this->setValidator('terms', new sfValidatorChoice(array('choices' => array('yes'))));

    $this->setValidator('password', new ValidatorPassword(array(
          'required' => true,
          'min_length' => 10,
          'max_length' => 100
      )));

    $this->getWidgetSchema()->setHelp('password', 'Your password must be at least 10 characters long, and include at least one number and one capital letter.');
    $this->getWidgetSchema()->setHelp('street', 'In accordance with our terms of service and legal obligations, you must provide your, or your organisations\' legal address.');

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
    
    $this->getWidgetSchema()->setLabel('vat', 'VAT no. (optional)');
    $this->mergePostValidator(new ValidatorVat(null, array('country' => 'country', 'vat' => 'vat')));
  }

}