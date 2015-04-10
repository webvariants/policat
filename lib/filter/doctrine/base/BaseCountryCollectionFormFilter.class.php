<?php
/*
 * Copyright (c) 2015, webvariants GmbH & Co. KG, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 * CountryCollection filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
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
