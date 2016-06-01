<?php

/**
 * TaxNote form base class.
 *
 * @method TaxNote getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseTaxNoteForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'   => new sfWidgetFormInputHidden(),
      'name' => new sfWidgetFormInputText(),
      'note' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name' => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'note' => new sfValidatorString(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'TaxNote', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('tax_note[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TaxNote';
  }

}
