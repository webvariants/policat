<?php

/**
 * Pledge form base class.
 *
 * @method Pledge getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BasePledgeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'pledge_item_id' => new sfWidgetFormInputHidden(),
      'contact_id'     => new sfWidgetFormInputHidden(),
      'status'         => new sfWidgetFormInputText(),
      'status_at'      => new sfWidgetFormDateTime(),
      'comment'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'pledge_item_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('pledge_item_id')), 'empty_value' => $this->getObject()->get('pledge_item_id'), 'required' => false)),
      'contact_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('contact_id')), 'empty_value' => $this->getObject()->get('contact_id'), 'required' => false)),
      'status'         => new sfValidatorInteger(array('required' => false)),
      'status_at'      => new sfValidatorDateTime(array('required' => false)),
      'comment'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pledge[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Pledge';
  }

}
