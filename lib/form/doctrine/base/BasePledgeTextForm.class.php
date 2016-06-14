<?php

/**
 * PledgeText form base class.
 *
 * @method PledgeText getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BasePledgeTextForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'pledge_item_id'   => new sfWidgetFormInputHidden(),
      'petition_text_id' => new sfWidgetFormInputHidden(),
      'text'             => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'pledge_item_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('pledge_item_id')), 'empty_value' => $this->getObject()->get('pledge_item_id'), 'required' => false)),
      'petition_text_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('petition_text_id')), 'empty_value' => $this->getObject()->get('petition_text_id'), 'required' => false)),
      'text'             => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('pledge_text[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PledgeText';
  }

}
