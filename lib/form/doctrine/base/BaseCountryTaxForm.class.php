<?php

/**
 * CountryTax form base class.
 *
 * @method CountryTax getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseCountryTaxForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'country'        => new sfWidgetFormInputText(),
      'tax_no_vat'     => new sfWidgetFormInputText(),
      'no_vat_note_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('NoVatNote'), 'add_empty' => true)),
      'tax_vat'        => new sfWidgetFormInputText(),
      'vat_note_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VatNote'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'country'        => new sfValidatorString(array('max_length' => 2)),
      'tax_no_vat'     => new sfValidatorNumber(),
      'no_vat_note_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('NoVatNote'), 'column' => 'id', 'required' => false)),
      'tax_vat'        => new sfValidatorNumber(),
      'vat_note_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('VatNote'), 'column' => 'id', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'CountryTax', 'column' => array('country')))
    );

    $this->widgetSchema->setNameFormat('country_tax[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CountryTax';
  }

}
