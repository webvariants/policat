<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UserNewForm extends BasesfGuardUserAdminForm {

  public function setup() {
    parent::setup();

    $this->widgetSchema->setFormFormatterName('bootstrap');
    $this->widgetSchema->setNameFormat('user_form[%s]');

    $this->setWidget('country', new sfWidgetFormI18nChoiceCountry);
    $this->setValidator('country', new sfValidatorI18nChoiceCountry);

    $this->setValidator('email_address', new ValidatorEmail(array('max_length' => 80)));

    foreach (array('email_address', 'first_name', 'last_name', 'organisation', 'website', 'street',
      'post_code', 'city', 'country', 'mobile', 'phone', 'language_id') as $field)
      $this->getValidator($field)->setOption('required', true);

    $this->useFields(array(
        'email_address', 'first_name', 'last_name', 'organisation', 'website', 'street',
        'post_code', 'city', 'country', 'mobile', 'phone', 'language_id', 'groups_list'
      ), true);

    $this->getWidget('groups_list')->setOption('expanded', true);

    $this->getWidgetSchema()->setHelp('street', 'In accordance with our terms of service and legal obligations, you must provide your, or your organisations\' legal address.');

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
          new sfValidatorDoctrineUnique(
            array('model' => 'sfGuardUser', 'column' => array('email_address')),
            array('invalid' => 'An user account with this email exists already.')
          ),
          new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username')))
      ))
    );
  }

}
