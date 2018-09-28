<?php
/*
 * Copyright (c) 2016, webvariants GmbH <?php Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class UserPasswordForm extends BasesfGuardRegisterForm {

  public function setup() {
    parent::setup();

    $this->widgetSchema->setFormFormatterName('bootstrap4');
    $this->widgetSchema->setNameFormat('password_form[%s]');

    foreach (array('password', 'password_again') as $field)
      $this->getValidator($field)->setOption('required', true);

    $this->useFields(array('password', 'password_again'), true);

    $this->setValidator('password', new ValidatorPassword(array(
          'required' => true,
          'min_length' => 10,
          'max_length' => 100
      )));

    $this->getValidator('password_again')->setOption('required', false);

    $this->getWidgetSchema()->setHelp('password', 'Your password must be at least 10 characters long, and include at least one number and one capital letter.');

    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.'))
    );
  }

}