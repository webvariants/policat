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
 * PetitionApiToken form base class.
 *
 * @method PetitionApiToken getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePetitionApiTokenForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'petition_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'add_empty' => false)),
      'name'        => new sfWidgetFormInputText(),
      'token'       => new sfWidgetFormInputText(),
      'status'      => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'petition_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Petition'), 'column' => 'id')),
      'name'        => new sfValidatorString(array('max_length' => 80)),
      'token'       => new sfValidatorString(array('max_length' => 80)),
      'status'      => new sfValidatorInteger(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'PetitionApiToken', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('petition_api_token[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PetitionApiToken';
  }

}
