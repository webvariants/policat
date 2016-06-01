<?php

/**
 * MappingPair form base class.
 *
 * @method MappingPair getObject() Returns the current form's model object
 *
 * @package    policat
 * @subpackage form
 * @author     developer-docker
 * @version    SVN: $Id$
 */
abstract class BaseMappingPairForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'mapping_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mapping'), 'add_empty' => false)),
      'a'              => new sfWidgetFormInputText(),
      'b'              => new sfWidgetFormInputText(),
      'object_version' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mapping_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mapping'), 'column' => 'id')),
      'a'              => new sfValidatorString(array('max_length' => 80)),
      'b'              => new sfValidatorString(array('max_length' => 80)),
      'object_version' => new sfValidatorString(array('max_length' => 15, 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'MappingPair', 'column' => array('mapping_id', 'a', 'b')))
    );

    $this->widgetSchema->setNameFormat('mapping_pair[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MappingPair';
  }

}
