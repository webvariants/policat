<?php

/**
 * CountryCollection filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseCountryCollectionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'countries' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'      => new sfValidatorPass(array('required' => false)),
      'countries' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('country_collection_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CountryCollection';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'name'      => 'Text',
      'countries' => 'Text',
    );
  }
}
