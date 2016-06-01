<?php

/**
 * CountryTax filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseCountryTaxFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'country'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'tax_no_vat'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'no_vat_note_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('NoVatNote'), 'add_empty' => true)),
      'tax_vat'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'vat_note_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('VatNote'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'country'        => new sfValidatorPass(array('required' => false)),
      'tax_no_vat'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'no_vat_note_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('NoVatNote'), 'column' => 'id')),
      'tax_vat'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'vat_note_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('VatNote'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('country_tax_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CountryTax';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'country'        => 'Text',
      'tax_no_vat'     => 'Number',
      'no_vat_note_id' => 'ForeignKey',
      'tax_vat'        => 'Number',
      'vat_note_id'    => 'ForeignKey',
    );
  }
}
