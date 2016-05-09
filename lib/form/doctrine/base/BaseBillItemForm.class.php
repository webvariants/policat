<?php

/**
 * BillItem form base class.
 *
 * @method BillItem getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseBillItemForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'      => new sfWidgetFormInputHidden(),
      'bill_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bill'), 'add_empty' => false)),
      'name'    => new sfWidgetFormInputText(),
      'price'   => new sfWidgetFormInputText(),
      'days'    => new sfWidgetFormInputText(),
      'emails'  => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'bill_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bill'), 'column' => 'id')),
      'name'    => new sfValidatorString(array('max_length' => 120)),
      'price'   => new sfValidatorNumber(),
      'days'    => new sfValidatorInteger(),
      'emails'  => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('bill_item[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'BillItem';
  }

}
