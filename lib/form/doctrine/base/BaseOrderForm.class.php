<?php

/**
 * Order form base class.
 *
 * @method Order getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseOrderForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'status'            => new sfWidgetFormInputText(),
      'paid_at'           => new sfWidgetFormDate(),
      'user_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'first_name'        => new sfWidgetFormInputText(),
      'last_name'         => new sfWidgetFormInputText(),
      'organisation'      => new sfWidgetFormInputText(),
      'street'            => new sfWidgetFormInputText(),
      'city'              => new sfWidgetFormInputText(),
      'post_code'         => new sfWidgetFormInputText(),
      'country'           => new sfWidgetFormInputText(),
      'vat'               => new sfWidgetFormInputText(),
      'tax'               => new sfWidgetFormInputText(),
      'tax_note'          => new sfWidgetFormTextarea(),
      'paypal_payment_id' => new sfWidgetFormInputText(),
      'paypal_sale_id'    => new sfWidgetFormInputText(),
      'paypal_status'     => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'status'            => new sfValidatorInteger(array('required' => false)),
      'paid_at'           => new sfValidatorDate(array('required' => false)),
      'user_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id', 'required' => false)),
      'first_name'        => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'last_name'         => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'organisation'      => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'street'            => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'city'              => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'post_code'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'country'           => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'vat'               => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'tax'               => new sfValidatorNumber(array('required' => false)),
      'tax_note'          => new sfValidatorString(array('required' => false)),
      'paypal_payment_id' => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'paypal_sale_id'    => new sfValidatorString(array('max_length' => 80, 'required' => false)),
      'paypal_status'     => new sfValidatorInteger(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Order';
  }

}
