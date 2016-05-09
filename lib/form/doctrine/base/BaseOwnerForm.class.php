<?php

/**
 * Owner form base class.
 *
 * @method Owner getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseOwnerForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'status'          => new sfWidgetFormInputText(),
      'campaign_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => false)),
      'first_widget_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FirstWidget'), 'add_empty' => true)),
      'firstname'       => new sfWidgetFormInputText(),
      'lastname'        => new sfWidgetFormInputText(),
      'function'        => new sfWidgetFormInputText(),
      'organisation'    => new sfWidgetFormInputText(),
      'email'           => new sfWidgetFormInputText(),
      'phone'           => new sfWidgetFormInputText(),
      'address'         => new sfWidgetFormInputText(),
      'country'         => new sfWidgetFormInputText(),
      'password'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'status'          => new sfValidatorInteger(array('required' => false)),
      'campaign_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id')),
      'first_widget_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('FirstWidget'), 'column' => 'id', 'required' => false)),
      'firstname'       => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'lastname'        => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'function'        => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'organisation'    => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'email'           => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'phone'           => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'address'         => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'country'         => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'password'        => new sfValidatorString(array('max_length' => 81, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('owner[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Owner';
  }

}
