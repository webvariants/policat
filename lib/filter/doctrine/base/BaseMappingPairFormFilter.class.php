<?php

/**
 * MappingPair filter form base class.
 *
 * @package    policat
 * @subpackage filter
 * @author     Martin
 * @version    SVN: $Id$
 */
abstract class BaseMappingPairFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mapping_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mapping'), 'add_empty' => true)),
      'a'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'b'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'object_version' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'mapping_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mapping'), 'column' => 'id')),
      'a'              => new sfValidatorPass(array('required' => false)),
      'b'              => new sfValidatorPass(array('required' => false)),
      'object_version' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('mapping_pair_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MappingPair';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'mapping_id'     => 'ForeignKey',
      'a'              => 'Text',
      'b'              => 'Text',
      'object_version' => 'Text',
    );
  }
}
