<?php

/**
 * Offer form base class.
 *
 * @method Offer getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseOfferForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'user_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'campaign_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'add_empty' => true)),
      'first_name'   => new sfWidgetFormInputText(),
      'last_name'    => new sfWidgetFormInputText(),
      'organisation' => new sfWidgetFormInputText(),
      'street'       => new sfWidgetFormInputText(),
      'city'         => new sfWidgetFormInputText(),
      'post_code'    => new sfWidgetFormInputText(),
      'country'      => new sfWidgetFormInputText(),
      'vat'          => new sfWidgetFormInputText(),
      'price'        => new sfWidgetFormInputText(),
      'tax'          => new sfWidgetFormInputText(),
      'tax_note'     => new sfWidgetFormTextarea(),
      'price_brutto' => new sfWidgetFormInputText(),
      'markup'       => new sfWidgetFormTextarea(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id', 'required' => false)),
      'campaign_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Campaign'), 'column' => 'id', 'required' => false)),
      'first_name'   => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'last_name'    => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'organisation' => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'street'       => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'city'         => new sfValidatorString(array('max_length' => 120, 'required' => false)),
      'post_code'    => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'country'      => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'vat'          => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'price'        => new sfValidatorNumber(),
      'tax'          => new sfValidatorNumber(),
      'tax_note'     => new sfValidatorString(array('required' => false)),
      'price_brutto' => new sfValidatorNumber(),
      'markup'       => new sfValidatorString(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('offer[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Offer';
  }

}
