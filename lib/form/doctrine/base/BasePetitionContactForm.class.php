<?php

/**
 * PetitionContact form base class.
 *
 * @method PetitionContact getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePetitionContactForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'petition_id'          => new sfWidgetFormInputHidden(),
      'contact_id'           => new sfWidgetFormInputHidden(),
      'secret'               => new sfWidgetFormInputText(),
      'password'             => new sfWidgetFormInputText(),
      'password_reset'       => new sfWidgetFormInputText(),
      'password_reset_until' => new sfWidgetFormDate(),
      'comment'              => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'petition_id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('petition_id')), 'empty_value' => $this->getObject()->get('petition_id'), 'required' => false)),
      'contact_id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('contact_id')), 'empty_value' => $this->getObject()->get('contact_id'), 'required' => false)),
      'secret'               => new sfValidatorString(array('max_length' => 40)),
      'password'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'password_reset'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'password_reset_until' => new sfValidatorDate(array('required' => false)),
      'comment'              => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('petition_contact[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PetitionContact';
  }

}
